@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Nova Oportunidade</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Cadastre uma possível negociação.</p>
        </div>
        <a href="{{ route('oportunidades.index') }}" class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-white">Voltar</a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('oportunidades.store') }}" class="space-y-6">
            @csrf
            @include('pages.oportunidades._form', ['oportunidade' => null])
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
