@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Editar Grupo</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize os dados do grupo.</p>
        </div>
        <a href="{{ route('grupos.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/50">Voltar</a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('grupos.update', $group) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input name="name" type="text" value="{{ old('name', $group->name) }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <input name="description" type="text" value="{{ old('description', $group->description) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Permissões do Grupo</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecione as features que este grupo pode usar.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @php($featuresList = $features ?? collect(config('permissions.features', [])))
                    @foreach($featuresList as $feature)
                        @php($checked = $group->permissions->contains('key', $feature['key']))
                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <input type="checkbox" name="permissions[{{ $feature['key'] }}]" value="1" {{ $checked ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600" />
                            <span>
                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-200">{{ $feature['name'] }}</span>
                                <span class="block text-xs text-gray-500">{{ $feature['key'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mt-2 flex items-center justify-end gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg border border-brand-600 bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 hover:border-brand-700">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
