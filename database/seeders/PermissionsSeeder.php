<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'consumption_create',
            ],
            [
                'name' => 'consumption_edit',
            ],
            [
                'name' => 'consumption_show',
            ],
            [
                'name' => 'consumption_access',
            ],
            [
                'name' => 'consumption_delete',
            ],
            [
                'name' => 'customer_create',
            ],
            [
                'name' => 'customer_edit',
            ],
            [
                'name' => 'customer_show',
            ],
            [
                'name' => 'customer_access',
            ],
            [
                'name' => 'customer_delete',
            ],
            [
                'name' => 'meter_reading_create',
            ],
            [
                'name' => 'meter_reading_edit',
            ],
            [
                'name' => 'meter_reading_show',
            ],
            [
                'name' => 'meter_reading_access',
            ],
            [
                'name' => 'meter_reading_delete',
            ],
            [
                'name' => 'meter_tariff_create',
            ],
            [
                'name' => 'meter_tariff_edit',
            ],
            [
                'name' => 'meter_tariff_show',
            ],
            [
                'name' => 'meter_tariff_access',
            ],
            [
                'name' => 'meter_tariff_delete',
            ],
            [
                'name' => 'meter_create',
            ],
            [
                'name' => 'meter_edit',
            ],
            [
                'name' => 'meter_show',
            ],
            [
                'name' => 'meter_access',
            ],
            [
                'name' => 'meter_delete',
            ],
            [
                'name' => 'permission_create',
            ],
            [
                'name' => 'permission_edit',
            ],
            [
                'name' => 'permission_show',
            ],
            [
                'name' => 'permission_access',
            ],
            [
                'name' => 'permission_delete',
            ],
            [
                'name' => 'role_create',
            ],
            [
                'name' => 'role_edit',
            ],
            [
                'name' => 'role_show',
            ],
            [
                'name' => 'role_access',
            ],
            [
                'name' => 'role_delete',
            ],
            [
                'name' => 'tariff_create',
            ],
            [
                'name' => 'tariff_edit',
            ],
            [
                'name' => 'tariff_show',
            ],
            [
                'name' => 'tariff_access',
            ],
            [
                'name' => 'tariff_delete',
            ],
            [
                'name' => 'tenant_create',
            ],
            [
                'name' => 'tenant_edit',
            ],
            [
                'name' => 'tenant_show',
            ],
            [
                'name' => 'tenant_access',
            ],
            [
                'name' => 'tenant_delete',
            ],
            [
                'name' => 'user_create',
            ],
            [
                'name' => 'user_edit',
            ],
            [
                'name' => 'user_show',
            ],
            [
                'name' => 'user_access',
            ],
            [
                'name' => 'user_delete',
            ],
            [
                'name' => 'utility_type_create',
            ],
            [
                'name' => 'utility_type_edit',
            ],
            [
                'name' => 'utility_type_show',
            ],
            [
                'name' => 'utility_type_access',
            ],
            [
                'name' => 'utility_type_delete',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()
                ->firstOrCreate(
                    ['name' => $permission['name']],
                );
        }
    }
}
