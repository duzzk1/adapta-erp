<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
        <input name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
        <input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
    </div>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
        <input name="password" type="password" @if(isset($user) && $user->exists) placeholder="Deixe em branco para manter" @endif class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Grupo</label>
        @php($groupList = $groups ?? \App\Models\UserGroup::orderBy('name','asc')->get())
        <select name="group_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            <option value="">Nenhum</option>
            @foreach($groupList as $g)
                <option value="{{ $g->id }}" @if(old('group_id', $user->group_id ?? null) == $g->id) selected @endif>{{ $g->name }}</option>
            @endforeach
        </select>
    </div>
</div>
