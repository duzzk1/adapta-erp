@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Permissões</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Defina quais grupos podem usar cada feature.</p>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-300">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-x-auto">
        <form method="POST" action="{{ route('permissoes.update') }}">
            @csrf
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">Feature</th>
                        @foreach($groups as $group)
                            <th class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">{{ $group->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($features as $feature)
                        @php($perm = $allPermissions[$feature['key']] ?? null)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                                <div class="font-medium">{{ $feature['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $feature['key'] }}</div>
                            </td>
                            @foreach($groups as $group)
                                @php($has = $group->permissions->contains('id', $perm->id))
                                <td class="px-3 py-2">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="permissions[{{ $group->id }}][{{ $feature['key'] }}]" value="1" {{ $has ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600" />
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
</div>
@endsection
