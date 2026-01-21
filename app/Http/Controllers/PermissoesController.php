<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissoesController extends Controller
{
    public function index()
    {
        if (Gate::denies('use-feature', 'permissoes.manage')) {
            abort(403);
        }

        $groups = UserGroup::orderBy('name', 'asc')->get();
        $features = collect(config('permissions.features'));

        // Ensure permissions exist for all features in config
        foreach ($features as $feature) {
            Permission::firstOrCreate(
                ['key' => $feature['key']],
                ['name' => $feature['name'], 'description' => $feature['name']]
            );
        }

        // Load permissions keyed by key for quick lookup
        $allPermissions = Permission::all()->keyBy('key');

        return view('pages.permissoes.index', [
            'title' => 'Permissões',
            'groups' => $groups,
            'features' => $features,
            'allPermissions' => $allPermissions,
        ]);
    }

    public function update(Request $request)
    {
        if (Gate::denies('use-feature', 'permissoes.manage')) {
            abort(403);
        }

        $data = $request->input('permissions', []);

        foreach ($data as $groupId => $permKeys) {
            $group = UserGroup::query()->find((int) $groupId);
            if (!$group) continue;

            $ids = Permission::query()->whereIn('key', array_keys($permKeys))->pluck('id')->toArray();
            $group->permissions()->sync($ids);
        }

        return back()->with('success', 'Permissões atualizadas.');
    }
}
