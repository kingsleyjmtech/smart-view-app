<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (config('app.env') === 'production' || config('app.env') === 'development') {
            $this->call([
                PermissionsSeeder::class,
                RolesSeeder::class,
                PermissionRoleSeeder::class,
                UsersSeeder::class,
                RoleUserSeeder::class,
            ]);
        } elseif (config('app.env') === 'local') {
            $this->call([
                PermissionsSeeder::class,
                RolesSeeder::class,
                PermissionRoleSeeder::class,
                UsersSeeder::class,
                RoleUserSeeder::class,
                UtilityTypeSeeder::class,
                LocalDataSeeder::class,
            ]);
        }
    }
}
