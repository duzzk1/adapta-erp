<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ApiPermission;
use App\Models\User;
use App\Models\Permission;
use App\Models\UserGroup;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate to control per-user API integration permissions
        Gate::define('use-api', function (User $user, string $apiKey) {
            return ApiPermission::query()
                ->where('user_id', $user->id)
                ->where('api_key', $apiKey)
                ->where('can_use', true)
                ->exists();
        });

        // Gate to control feature access via user group permissions
        Gate::define('use-feature', function (User $user, string $featureKey) {
            if (!$user->group_id) {
                return false;
            }
            return Permission::query()
                ->where('key', $featureKey)
                ->whereHas('userGroups', function ($q) use ($user) {
                    $q->where('user_groups.id', $user->group_id);
                })
                ->exists();
        });
    }
}
