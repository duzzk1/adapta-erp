<!-- Top: centered duration -->
<div class="flex items-stretch justify-center">
    <div class="w-full md:w-2/3 lg:w-1/2">
        <x-calls.summary-duration :totalizerCallTime="$totalizerCallTime" />
    </div>
    
</div>

<!-- Content area: charts left, messenger-style ranking right -->
<div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3 items-stretch">
    <!-- Left: charts and totals -->
    <div class="md:col-span-2 space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 items-stretch">
            <div class="h-full">
                <x-calls.summary-total :total="$total" :callStatus="$callStatus" />
            </div>
            <div class="h-full rounded-xl border border-gray-200 bg-white p-4 shadow-sm overflow-hidden dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Chamadas por Status</h3>
                </div>
                <div id="chartCallsStatus" class="w-full max-w-full" style="height: 220px" data-calls-status='@json($statusCounts)'></div>
            </div>
        </div>
        <!-- Extra KPIs -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="text-xs text-gray-500 dark:text-gray-400">Chamadas Hoje</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $callsToday }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="text-xs text-gray-500 dark:text-gray-400">Taxa de Atendimento</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $answerRate }}%</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="text-xs text-gray-500 dark:text-gray-400">Duração Média</div>
                @php($avgH = str_pad((string)intdiv($avgDurationSec, 3600), 2, '0', STR_PAD_LEFT))
                @php($avgM = str_pad((string)intdiv($avgDurationSec % 3600, 60), 2, '0', STR_PAD_LEFT))
                @php($avgS = str_pad((string)($avgDurationSec % 60), 2, '0', STR_PAD_LEFT))
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $avgH }}:{{ $avgM }}:{{ $avgS }}</div>
            </div>
        </div>
        <!-- 7-day trend chart -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm overflow-hidden dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Chamadas (últimos 7 dias)</h3>
            </div>
            <div id="chartCalls7Days" class="w-full max-w-full" style="height: 180px" data-calls-7days='@json($calls7Days)'></div>
        </div>
    </div>
    <!-- Right: ranking chat panel -->
    <div class="md:col-span-1">
        <x-calls.ranking :ranking="$ranking" />
    </div>
</div>
