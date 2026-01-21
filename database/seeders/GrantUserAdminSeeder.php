<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Permission;

class GrantUserAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Admin group exists
        $adminGroup = UserGroup::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Acesso total ao sistema']
        );

        // Ensure all permissions exist and are granted to Admin
        $features = config('permissions.features', []);
        foreach ($features as $feature) {
            $key = $feature['key'] ?? null;
            if (!$key) { continue; }
            Permission::firstOrCreate(
                ['key' => $key],
                ['name' => $feature['name'] ?? $key, 'description' => $feature['description'] ?? null]
            );
        }
        $adminGroup->permissions()->sync(Permission::pluck('id')->all());

        // Target user: admin@somos.local
        $user = User::where('email', 'admin@somos.local')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@somos.local',
                'password' => 'admin123',
            ]);
        }

        // Assign Admin group
        if ($user->group_id !== $adminGroup->id) {
            $user->group_id = $adminGroup->id;
            $user->save();
        }

        $this->command->info('Usuário admin@somos.local agora é Admin com todas as permissões.');
    }
}
