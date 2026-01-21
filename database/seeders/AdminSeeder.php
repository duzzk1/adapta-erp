<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure permissions exist from config
        $features = config('permissions.features', []);
        foreach ($features as $feature) {
            $key = $feature['key'] ?? null;
            if (!$key) { continue; }

            Permission::firstOrCreate(
                ['key' => $key],
                ['name' => $feature['name'] ?? $key, 'description' => $feature['description'] ?? null]
            );
        }

        // Create or get Admin group
        $adminGroup = UserGroup::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Acesso total ao sistema']
        );

        // Grant all permissions to Admin group
        $allPermissionIds = Permission::pluck('id')->all();
        $adminGroup->permissions()->sync($allPermissionIds);

        // Create or get admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@somos.local'],
            ['name' => 'Administrador', 'password' => 'admin123']
        );

        // Assign group to admin user
        if ($admin->group_id !== $adminGroup->id) {
            $admin->group_id = $adminGroup->id;
            $admin->save();
        }
    }
}
