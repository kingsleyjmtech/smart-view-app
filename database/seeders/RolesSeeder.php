<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'User',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()
                ->firstOrCreate(
                    ['name' => $role['name']],
                    $role
                );
        }
    }
}
