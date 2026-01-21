@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Monitor de Fila</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Acompanhe o processamento em tempo real.</p>
    </div>

    <div x-data="{ runs: @js($runs), timers: {}, refreshRow(run) { 
            fetch(`{{ url('/integracoes/progresso') }}/${run.run_id}`)
                .then(r => r.json())
                .then(json => { 
                    run.status = json.status || run.status; 
                    run.current = json.current ?? run.current; 
                    run.total = json.total ?? run.total; 
                    run.message = json.message ?? run.message; 
                    run.updated_at = json.updated_at ?? run.updated_at; 
                    if (json.status === 'completed' && this.timers[run.run_id]) { 
                        clearInterval(this.timers[run.run_id]); 
                        delete this.timers[run.run_id]; 
                    }
                });
        }, startPolling() { 
            this.runs.forEach(run => { 
                if (run.status === 'running') { 
                    this.refreshRow(run);
                    this.timers[run.run_id] = setInterval(() => this.refreshRow(run), 2000);
                }
            });
        } }" x-init="startPolling()" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Run ID</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Job</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Mensagem</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Progresso</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Atualizado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="run in runs" :key="run.run_id">
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-800 dark:text-gray-200" x-text="run.run_id"></td>
                            <td class="px-3 py-2 text-sm text-gray-800 dark:text-gray-200" x-text="run.job"></td>
                            <td class="px-3 py-2 text-sm">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs" :class="{
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': run.status === 'running',
                                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': run.status === 'completed',
                                    'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300': run.status === 'failed',
                                }" x-text="run.status"></span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300" x-text="run.message"></td>
                            <td class="px-3 py-2">
                                <div class="h-2 w-40 rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded-full bg-primary" :style="`width: ${run.total ? Math.round((run.current/run.total)*100) : 0}%`"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400" x-text="`${run.current} / ${run.total}`"></div>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-400" x-text="run.updated_at"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
@endsection
