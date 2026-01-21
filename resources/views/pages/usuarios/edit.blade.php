@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Editar Usuário</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize os dados do usuário.</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/50">Voltar</a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('usuarios.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('pages.usuarios._form', ['groups' => $groups])
            <div class="mt-2 flex items-center justify-end gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
