<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GruposController extends Controller
{
    public function index()
    {
        if (Gate::denies('use-feature', 'grupos.manage')) {
            abort(403);
        }
        $groups = UserGroup::orderBy('name', 'asc')->paginate(10);
        return view('pages.grupos.index', [
            'title' => 'Grupos',
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        if (Gate::denies('use-feature', 'grupos.manage')) {
            abort(403);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        UserGroup::create($data);
        return redirect()->route('grupos.index')->with('success', 'Grupo criado com sucesso.');
    }

    public function edit(UserGroup $grupo)
    {
        if (Gate::denies('use-feature', 'grupos.manage')) {
            abort(403);
        }
        $grupo->load('permissions');
        $features = collect(config('permissions.features', []));
        return view('pages.grupos.edit', [
            'title' => 'Editar Grupo',
            'group' => $grupo,
            'features' => $features,
        ]);
    }

    public function update(Request $request, UserGroup $grupo)
    {
        if (Gate::denies('use-feature', 'grupos.manage')) {
            abort(403);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['in:' . collect(config('permissions.features', []))->pluck('key')->implode(',')],
        ]);
        $grupo->fill($data);
        $grupo->save();

        // Sync group permissions by feature keys from config
        $selectedKeys = array_keys($request->input('permissions', []));
        $permissionIds = \App\Models\Permission::query()
            ->whereIn('key', $selectedKeys)
            ->pluck('id')
            ->all();
        $grupo->permissions()->sync($permissionIds);
        return redirect()->route('grupos.index')->with('success', 'Grupo atualizado com sucesso.');
    }

    public function destroy(UserGroup $grupo)
    {
        if (Gate::denies('use-feature', 'grupos.manage')) {
            abort(403);
        }
        UserGroup::destroy($grupo->id);
        return redirect()->route('grupos.index')->with('success', 'Grupo removido.');
    }
}
