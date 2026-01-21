<?php

namespace App\Jobs;

use App\Models\Calls;
use App\Models\JobProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportCallsCSV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $runId;

    public function __construct(string $runId)
    {
        $this->runId = $runId;
    }

    public function handle(): void
    {
        $path = 'app/private/calls.csv';
        $full = storage_path($path);

        if (!file_exists($full)) {
            return;
        }

        if (($handle = fopen($full, 'r')) === false) {
            return;
        }

        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            fclose($handle);
            return;
        }
        // Pre-count total rows for progress
        $total = 0;
        while (fgetcsv($handle, 0, ',') !== false) {
            $total++;
        }
        // Reset to start of data
        rewind($handle);
        fgetcsv($handle, 0, ','); // skip headers

        JobProgress::updateOrCreate(
            ['run_id' => $this->runId],
            ['job' => 'csv_import', 'status' => 'running', 'current' => 0, 'total' => $total, 'message' => 'Iniciando importação CSV']
        );

        $current = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $data = array_combine($headers, $row);
            if (!$data) {
                continue;
            }

            $seconds = isset($data['duration']) ? (int) $data['duration'] : 0;
            $h = str_pad((string) intdiv($seconds, 3600), 2, '0', STR_PAD_LEFT);
            $m = str_pad((string) intdiv($seconds % 3600, 60), 2, '0', STR_PAD_LEFT);
            $s = str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT);
            $duration = "$h:$m:$s";

            $payload = [
                'direction' => $data['direction'] ?? null,
                'status' => $data['status'] ?? null,
                'from' => $data['from'] ?? null,
                'to' => $data['to'] ?? null,
                'call_time' => $duration,
                'call_date' => $data['date'] ?? null,
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
            JobProgress::query()->where('run_id', $this->runId)->update([
                'current' => $current,
                'message' => 'Processando: ' . ($data['from'] ?? '') . ' -> ' . ($data['to'] ?? ''),
            ]);
        }

        fclose($handle);

        JobProgress::query()->where('run_id', $this->runId)->update([
            'status' => 'completed',
            'message' => 'Importação CSV concluída',
        ]);
    }
}

