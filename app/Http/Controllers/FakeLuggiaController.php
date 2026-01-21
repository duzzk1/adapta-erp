<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FakeLuggiaController extends Controller
{
    public function calls(Request $request)
    {
        // Allow large datasets by default to simulate slower imports
        $limit = (int) $request->query('limit', 3000);
        // Clamp to avoid extreme memory usage
        if ($limit < 1) { $limit = 1; }
        if ($limit > 10000) { $limit = 10000; }
        $startDate = $request->query('start', now()->subDays(7)->format('Y-m-d 00:00:00'));
        $endDate = $request->query('end', now()->format('Y-m-d 23:59:59'));

        $directions = ['Outbound', 'Inbound'];
        $statuses = ['Answered', 'Unanswered', 'Scheduled'];
        $names = [
            'Ana Silva', 'Carlos Souza', 'Marcos Lima', 'Aline Rocha', 'João Pereira',
            'Pedro Alves', 'Beatriz Costa', 'Lucas Dias', 'Rafaela Martins', 'Gabriel Nunes',
            'Equipe Vendas', 'Suporte', 'Central', 'Cliente ABC', 'Cliente XYZ', 'Atendimento'
        ];

        $data = [];
        for ($i = 1; $i <= $limit; $i++) {
            $from = $names[array_rand($names)];
            $to = $names[array_rand($names)];
            while ($to === $from) {
                $to = $names[array_rand($names)];
            }
            $seconds = rand(0, 3600);

            // Random date between start and end
            $startTs = strtotime($startDate);
            $endTs = strtotime($endDate);
            if ($endTs <= $startTs) {
                $endTs = $startTs + 86400;
            }
            $ts = rand($startTs, $endTs);

            $data[] = [
                'id' => $i,
                'direction' => $directions[array_rand($directions)],
                'status' => $statuses[array_rand($statuses)],
                'from' => $from,
                'to' => $to,
                'duration' => $seconds,
                'date' => date('Y-m-d H:i:s', $ts),
            ];
        }

        return response()->json($data);
    }
}
