@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Colaboradores</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie os colaboradores da sua empresa.</p>
        </div>
        <button type="button" @click="openCreate = true" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Novo Colaborador</button>
    </div>

    <!-- Create Modal -->
    <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="openCreate = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 w-full max-w-xl rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Novo Colaborador</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Preencha os dados do colaborador.</p>

            <form method="POST" action="{{ route('colaboradores.store') }}" class="mt-4 space-y-4">
                @csrf
                @include('pages.colaboradores._form')
                <div class="mt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="openCreate = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/50">Cancelar</button>
                    <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-300">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="text-gray-600 dark:text-gray-300">
                        <th class="px-3 py-2">Nome</th>
                        <th class="px-3 py-2">E-mail</th>
                        <th class="px-3 py-2">Telefone</th>
                        <th class="px-3 py-2">Cargo</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Admissão</th>
                        <th class="px-3 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collaborators as $c)
                        <tr class="border-t border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                            <td class="px-3 py-2">{{ $c->name }}</td>
                            <td class="px-3 py-2">{{ $c->email }}</td>
                            <td class="px-3 py-2">{{ $c->phone }}</td>
                            <td class="px-3 py-2">{{ $c->role }}</td>
                            <td class="px-3 py-2">
                                @php($statusClass = $c->status === 'active'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700/40 dark:text-gray-300')
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $statusClass }}">
                                    {{ $c->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-3 py-2">{{ optional($c->hired_at)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('colaboradores.edit', $c) }}" class="text-primary hover:underline mr-3">Editar</a>
                                <form action="{{ route('colaboradores.destroy', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">Nenhum colaborador encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $collaborators->links() }}</div>
    </div>
</div>
@endsection
