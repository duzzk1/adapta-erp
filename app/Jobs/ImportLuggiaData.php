<?php

namespace App\Jobs;

use App\Models\Calls;
use App\Models\JobProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportLuggiaData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $baseUrl;
    public string $user;
    public string $pass;

    public string $runId;

    public function __construct(string $baseUrl, string $user, string $pass, string $runId)
    {
        $this->baseUrl = $baseUrl;
        $this->user = $user;
        $this->pass = $pass;
        $this->runId = $runId;
    }

    public function handle(): void
    {
        // Resolve base URL, allowing fake API via env when not provided
        $base = $this->baseUrl;
        if (!$base) {
            $fakeEnabled = filter_var(env('LUGGIA_FAKE_ENABLED', false), FILTER_VALIDATE_BOOL);
            $fakeUrl = env('LUGGIA_FAKE_URL');
            if ($fakeEnabled && $fakeUrl) {
                $base = rtrim($fakeUrl, '/');
            } else {
                // Missing credentials; exit
                return;
            }
        }
        // If base is relative, prepend app.url or sensible default
        if (strpos($base, 'http://') !== 0 && strpos($base, 'https://') !== 0) {
            $appUrl = rtrim(config('app.url', env('APP_URL', '')), '/');
            if (!$appUrl) {
                $appUrl = 'http://127.0.0.1:8000';
            }
            $base = $appUrl . '/' . ltrim($base, '/');
        }

        // Avoid duplicating /calls
        if (preg_match('/\/calls$/', $base)) {
            $url = $base;
        } else {
            $url = rtrim($base, '/') . '/calls';
        }
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Basic ' . base64_encode("{$this->user}:{$this->pass}"),
                'timeout' => 15,
            ],
        ];
        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            JobProgress::updateOrCreate(
                ['run_id' => $this->runId],
                ['job' => 'luggia_import', 'status' => 'failed', 'current' => 0, 'total' => 0, 'message' => 'Falha ao buscar dados da API: ' . $url]
            );
            return;
        }
        $data = json_decode($body, true);
        if (!is_array($data)) {
            JobProgress::updateOrCreate(
                ['run_id' => $this->runId],
                ['job' => 'luggia_import', 'status' => 'failed', 'current' => 0, 'total' => 0, 'message' => 'Resposta inválida da API']
            );
            return;
        }

        $total = count($data);
        JobProgress::updateOrCreate(
            ['run_id' => $this->runId],
            ['job' => 'luggia_import', 'status' => 'running', 'current' => 0, 'total' => $total, 'message' => 'Iniciando importação Luggia']
        );

        $current = 0;
        foreach ($data as $row) {
            $seconds = isset($row['duration']) ? (int) $row['duration'] : 0;
            $h = str_pad((string) intdiv($seconds, 3600), 2, '0', STR_PAD_LEFT);
            $m = str_pad((string) intdiv($seconds % 3600, 60), 2, '0', STR_PAD_LEFT);
            $s = str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT);
            $duration = "$h:$m:$s";

            $payload = [
                'direction' => $row['direction'] ?? null,
                'status' => $row['status'] ?? null,
                'from' => $row['from'] ?? null,
                'to' => $row['to'] ?? null,
                'call_time' => $duration,
                'call_date' => $row['date'] ?? null,
            ];

            $unique = [
                'from' => $payload['from'],
                'to' => $payload['to'],
                'call_date' => $payload['call_date'],
                'call_time' => $payload['call_time'],
            ];

            if ($unique['from'] && $unique['to'] && $unique['call_date'] && $unique['call_time']) {
                Calls::firstOrCreate($unique, $payload);
            }

            $current++;
            JobProgress::query()->where('run_id', '=', $this->runId, 'and')->update([
                'current' => $current,
                'message' => 'Processando: ' . ($payload['from'] ?? '') . ' -> ' . ($payload['to'] ?? ''),
            ]);
        }

        JobProgress::query()->where('run_id', '=', $this->runId, 'and')->update([
            'status' => 'completed',
            'message' => 'Importação Luggia concluída',
        ]);
    }
}