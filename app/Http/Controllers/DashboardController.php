<?php

namespace App\Http\Controllers;

use App\Models\Calls;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
    }

    public function filter(Request $request)
    {
        session()->put('filter_start_date', $request->input('start_date'));
        session()->put('filter_end_date', $request->input('end_date'));
        return redirect()->route('dashboard.calls');
    }

    public function calls(Request $request)
    {
        $startDate = session('filter_start_date');
        $endDate = session('filter_end_date');

        $callsQuery = Calls::query();

        if ($startDate && $endDate) {
            $callsQuery->whereBetween('call_date', [
                Carbon::parse($startDate),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($startDate) {
            $callsQuery->whereDate('call_date', '>=', Carbon::parse($startDate));
        } elseif ($endDate) {
            $callsQuery->whereDate('call_date', '<=', Carbon::parse($endDate));
        }

        $total = (clone $callsQuery)->count();

        $ranking = (clone $callsQuery)
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('CASE WHEN direction = "Outbound" THEN `from` ELSE `to` END AS user_name')
            )
            ->groupBy('user_name')
            ->orderByDesc('total')
            ->get();

        $totalizerCallTime = (clone $callsQuery)
            ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(call_time))) as total_duration'))
            ->get();

        // Additional KPIs
        $answeredCount = (clone $callsQuery)->whereIn('status', ['Answered', 'Completed', 'Success'])->count();
        $missedCount = (clone $callsQuery)->whereIn('status', ['Unanswered', 'No Answer', 'Busy', 'Failed'])->count();
        $answerRate = $answeredCount + $missedCount > 0 ? round(($answeredCount / ($answeredCount + $missedCount)) * 100) : 0;

        $avgDurationRow = (clone $callsQuery)
            ->select(DB::raw('SUM(TIME_TO_SEC(call_time)) as total_sec'), DB::raw('COUNT(*) as cnt'))
            ->first();
        $avgDurationSec = ($avgDurationRow && $avgDurationRow->cnt) ? (int) ($avgDurationRow->total_sec / $avgDurationRow->cnt) : 0;

        $callsToday = (clone $callsQuery)->whereDate('call_date', Carbon::today())->count();

        // Calls last 7 days
        $start7 = Carbon::today()->subDays(6);
        $byDayRaw = (clone $callsQuery)
            ->select(DB::raw('DATE(call_date) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('call_date', [$start7, Carbon::today()->endOfDay()])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();
        $calls7Days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start7->copy()->addDays($i)->format('Y-m-d');
            $calls7Days[$day] = $byDayRaw[$day] ?? 0;
        }

        $callStatus = [
            'atendidas' => (clone $callsQuery)->where('status', 'Answered')->get(),
            'nao_atendidas' => (clone $callsQuery)->where('status', 'Unanswered')->get(),
            'agendadas' => (clone $callsQuery)->where('status', 'Scheduled')->get(),
        ];

        $statusCounts = [
            'Answered' => $callStatus['atendidas']->count(),
            'Unanswered' => $callStatus['nao_atendidas']->count(),
            'Scheduled' => $callStatus['agendadas']->count(),
        ];

        return view('pages.dashboard.calls', [
            'title' => 'Chamadas',
            'total' => $total,
            'ranking' => $ranking,
            'callStatus' => $callStatus,
            'totalizerCallTime' => $totalizerCallTime->pluck('total_duration')[0] ?? '00:00:00',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'answeredCount' => $answeredCount,
            'missedCount' => $missedCount,
            'answerRate' => $answerRate,
            'avgDurationSec' => $avgDurationSec,
            'callsToday' => $callsToday,
            'calls7Days' => $calls7Days,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function callsFragment(Request $request)
    {
        $startDate = session('filter_start_date');
        $endDate = session('filter_end_date');

        $callsQuery = Calls::query();

        if ($startDate && $endDate) {
            $callsQuery->whereBetween('call_date', [
                Carbon::parse($startDate),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($startDate) {
            $callsQuery->whereDate('call_date', '>=', Carbon::parse($startDate));
        } elseif ($endDate) {
            $callsQuery->whereDate('call_date', '<=', Carbon::parse($endDate));
        }

        $total = (clone $callsQuery)->count();

        $ranking = (clone $callsQuery)
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('CASE WHEN direction = "Outbound" THEN `from` ELSE `to` END AS user_name')
            )
            ->groupBy('user_name')
            ->orderByDesc('total')
            ->get();

        $totalizerCallTime = (clone $callsQuery)
            ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(call_time))) as total_duration'))
            ->get();

        // Additional KPIs
        $answeredCount = (clone $callsQuery)->whereIn('status', ['Answered', 'Completed', 'Success'])->count();
        $missedCount = (clone $callsQuery)->whereIn('status', ['Unanswered', 'No Answer', 'Busy', 'Failed'])->count();
        $answerRate = $answeredCount + $missedCount > 0 ? round(($answeredCount / ($answeredCount + $missedCount)) * 100) : 0;

        $avgDurationRow = (clone $callsQuery)
            ->select(DB::raw('SUM(TIME_TO_SEC(call_time)) as total_sec'), DB::raw('COUNT(*) as cnt'))
            ->first();
        $avgDurationSec = ($avgDurationRow && $avgDurationRow->cnt) ? (int) ($avgDurationRow->total_sec / $avgDurationRow->cnt) : 0;

        $callsToday = (clone $callsQuery)->whereDate('call_date', Carbon::today())->count();

        // Calls last 7 days
        $start7 = Carbon::today()->subDays(6);
        $byDayRaw = (clone $callsQuery)
            ->select(DB::raw('DATE(call_date) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('call_date', [$start7, Carbon::today()->endOfDay()])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();
        $calls7Days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start7->copy()->addDays($i)->format('Y-m-d');
            $calls7Days[$day] = $byDayRaw[$day] ?? 0;
        }

        $callStatus = [
            'atendidas' => (clone $callsQuery)->where('status', 'Answered')->get(),
            'nao_atendidas' => (clone $callsQuery)->where('status', 'Unanswered')->get(),
            'agendadas' => (clone $callsQuery)->where('status', 'Scheduled')->get(),
        ];

        $statusCounts = [
            'Answered' => $callStatus['atendidas']->count(),
            'Unanswered' => $callStatus['nao_atendidas']->count(),
            'Scheduled' => $callStatus['agendadas']->count(),
        ];

        return view('pages.dashboard._calls-content', [
            'total' => $total,
            'ranking' => $ranking,
            'callStatus' => $callStatus,
            'totalizerCallTime' => $totalizerCallTime->pluck('total_duration')[0] ?? '00:00:00',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'answeredCount' => $answeredCount,
            'missedCount' => $missedCount,
            'answerRate' => $answerRate,
            'avgDurationSec' => $avgDurationSec,
            'callsToday' => $callsToday,
            'calls7Days' => $calls7Days,
            'statusCounts' => $statusCounts,
        ]);
    }
}
