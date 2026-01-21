@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Integrações</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Importe dados de chamadas via API ou CSV.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('run_id'))
        <div x-data="{ data: { status: 'running', current: 0, total: 0, message: '' }, timer: null }" x-init="
            const fetchProgress = async () => {
                try {
                    const res = await fetch('{{ route('integracoes.progress', ['runId' => session('run_id')]) }}');
                    const json = await res.json();
                    data = json;
                    if (json.status === 'completed') clearInterval(timer);
                } catch (e) {}
            };
            fetchProgress();
            timer = setInterval(fetchProgress, 1500);
        " class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Monitor de Fila</h3>
                <span class="text-xs text-gray-600 dark:text-gray-400">Run: {{ session('run_id') }}</span>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="data.message || 'Aguardando atualização...' "></p>
            <div class="mt-3 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                <div class="h-2 rounded-full bg-primary" :style="`width: ${data.total ? Math.round((data.current/data.total)*100) : 0}%`"></div>
            </div>
            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400" x-text="`${data.current} / ${data.total}`"></div>
            <div class="mt-2 text-xs">
                <span class="inline-flex items-center rounded-full px-2 py-0.5" :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': data.status === 'running',
                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': data.status === 'completed',
                }" x-text="data.status"></span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        @foreach($integrations as $integration)
            <div x-data="{ open: false }" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $integration['name'] }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $integration['description'] }}</p>
                </div>
                @can('use-feature', $integration['key'] === 'luggia' ? 'integrations.luggia' : ($integration['key'] === 'csv' ? 'integrations.csv' : 'integrations.view'))
                @if($integration['key'] === 'luggia')
                    <button @click="open = true" type="button" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40">
                        Importar
                    </button>

                    <!-- Modal -->
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                        <div @click="open = false" class="absolute inset-0 bg-black/40"></div>
                        <div class="relative z-10 w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Credenciais da Luggia</h4>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Informe os dados para autenticação e URL da API.</p>

                            <form x-data="{ fakeEnabled: {{ ($fakeEnabled ?? false) ? 'true' : 'false' }}, editUrl: {{ $savedBaseUrl ? 'false' : 'true' }}, editCreds: {{ $savedUser ? 'false' : 'true' }} }" class="mt-4 space-y-4" method="POST" action="{{ route('integracoes.search', ['api' => 'luggia']) }}">
                                @csrf
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Base URL</label>
                                        @if($savedBaseUrl)
                                            <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                <input type="checkbox" x-model="editUrl" class="rounded border-gray-300 text-primary focus:ring-primary" />
                                                Alterar URL
                                            </label>
                                        @endif
                                    </div>
                                    @if(($fakeEnabled ?? false) && !empty($fakeUrl))
                                        <p class="mb-2 text-xs text-gray-600 dark:text-gray-400">Usando API fake do env: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $fakeUrl }}</span></p>
                                    @endif
                                    @if($savedBaseUrl)
                                        <p class="mb-2 text-xs text-gray-600 dark:text-gray-400">Atual: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $savedBaseUrl }}</span></p>
                                    @endif
                                    <input name="base_url" type="url" :required="editUrl && !fakeEnabled" :disabled="!editUrl || fakeEnabled" value="{{ $savedBaseUrl }}" placeholder="https://api.exemplo.com" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary disabled:opacity-60 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
                                    <input type="hidden" name="edit_url" :value="editUrl ? 1 : 0" />
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Usuário</label>
                                            @if($savedUser)
                                                <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" x-model="editCreds" class="rounded border-gray-300 text-primary focus:ring-primary" />
                                                    Alterar credenciais
                                                </label>
                                            @endif
                                        </div>
                                        @if($savedUser)
                                            <p class="mb-2 text-xs text-gray-600 dark:text-gray-400">Atual: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $savedUser }}</span></p>
                                        @endif
                                        <input name="user" type="text" :required="editCreds" :disabled="!editCreds && {{ $savedUser ? 'true' : 'false' }}" value="{{ $savedUser }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary disabled:opacity-60 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                                        <input name="pass" type="password" :required="editCreds" :disabled="!editCreds && {{ $savedUser ? 'true' : 'false' }}" placeholder="••••••••" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary disabled:opacity-60 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
                                        <input type="hidden" name="edit_creds" :value="editCreds ? 1 : 0" />
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-end gap-2">
                                    <button type="button" @click="open = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/50">Cancelar</button>
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Iniciar Importação</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('integracoes.search', ['api' => $integration['key']]) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/40">
                            Importar
                        </button>
                    </form>
                @endif
                @else
                    <div class="rounded-md border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800 dark:border-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                        Você não tem permissão para usar esta integração.
                    </div>
                @endcan
            </div>
        @endforeach
    </div>
    
    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Permissões de Uso</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Defina quem pode usar cada integração.</p>

        <form method="POST" action="{{ route('integracoes.permissions.update') }}" class="mt-4 overflow-x-auto">
            @csrf
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">Usuário</th>
                        @foreach($integrations as $integration)
                            <th class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">{{ $integration['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(($users ?? collect()) as $user)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $user->name }} <span class="text-xs text-gray-500">(#{{ $user->id }})</span></td>
                            @foreach($integrations as $integration)
                                @php
                                    $perm = ($permissions[$integration['key']][$user->id] ?? null);
                                    $checked = $perm ? ($perm->can_use ? 'checked' : '') : '';
                                @endphp
                                <td class="px-3 py-2">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="permissions[{{ $integration['key'] }}][{{ $user->id }}]" value="1" {{ $checked }} class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600" />
                                        <span class="text-gray-700 dark:text-gray-300">Permitir</span>
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 flex items-center justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Salvar Permissões</button>
            </div>
        </form>
    </div>
@endsection
