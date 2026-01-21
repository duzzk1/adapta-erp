@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Grupos</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie grupos de usuários.</p>
        </div>
        <button type="button" @click="openCreate = true" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Novo Grupo</button>
    </div>

    <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="openCreate = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 w-full max-w-xl rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Novo Grupo</h4>
            <form method="POST" action="{{ route('grupos.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                    <input name="name" type="text" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                    <input name="description" type="text" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
                </div>
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
                        <th class="px-3 py-2">Descrição</th>
                        <th class="px-3 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groups as $g)
                        <tr class="border-t border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                            <td class="px-3 py-2">{{ $g->name }}</td>
                            <td class="px-3 py-2">{{ $g->description }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('grupos.edit', $g) }}" class="text-primary hover:underline mr-3">Editar</a>
                                <form action="{{ route('grupos.destroy', $g) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">Nenhum grupo encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $groups->links() }}</div>
    </div>
</div>
@endsection
