<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminPermissions = Permission::all();
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->firstOrFail();
        $adminRole->permissions()
            ->sync($adminPermissions->pluck('id'));

        /*
        if (config('app.env') === 'local') {
            $userPermissions = $adminPermissions->filter(function ($permission) {
                return ! str_starts_with($permission->name, 'user_') &&
                    ! str_starts_with($permission->name, 'role_') &&
                    ! str_starts_with($permission->name, 'permission_');
            });

            $userRole = Role::query()
                ->where('name', 'User')
                ->firstOrFail();
            $userRole->permissions()
                ->sync($userPermissions->pluck('id'));
        }
        */
    }
}
