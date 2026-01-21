<div class="space-y-4">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
        <input type="text" name="name" value="{{ old('name', $collaborator->name ?? '') }}" required
               class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $collaborator->email ?? '') }}" required
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone', $collaborator->phone ?? '') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
            <input type="text" name="role" value="{{ old('role', $collaborator->role ?? '') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="active" @selected(old('status', isset($collaborator) ? $collaborator->status : 'active') === 'active')>Ativo</option>
                <option value="inactive" @selected(old('status', isset($collaborator) ? $collaborator->status : 'active') === 'inactive')>Inativo</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Admissão</label>
                     <input type="text" name="hired_at" value="{{ old('hired_at', (isset($collaborator) && $collaborator->hired_at) ? $collaborator->hired_at->format('Y-m-d') : '') }}" placeholder="Selecione a data"
                         class="datepicker w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-400" />
        </div>
    </div>
</div>
