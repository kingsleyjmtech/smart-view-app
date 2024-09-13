<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->firstOrFail();

        User::query()
            ->where('email', config('app.admin_user_email'))
            ->firstOrFail()
            ->roles()
            ->sync($adminRole->id);
    }
}