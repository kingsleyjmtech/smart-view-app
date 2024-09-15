<?php

namespace Database\Seeders;

use App\Models\UtilityType;
use Illuminate\Database\Seeder;

class UtilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $utilityTypes = [
            [
                'name' => 'Electricity',
                'description' => 'Electricity Utility',
                'status' => 'Active',
            ],
            [
                'name' => 'Water',
                'description' => 'Water Utility',
                'status' => 'Active',
            ],
            [
                'name' => 'Gas',
                'description' => 'Gas Utility',
                'status' => 'Active',
            ],
            [
                'name' => 'Solar',
                'description' => 'Solar Utility',
                'status' => 'Active',
            ],
        ];

        foreach ($utilityTypes as $type) {
            UtilityType::firstOrCreate(
                [
                    'name' => $type['name'],
                ],
                [
                    'description' => $type['description'],
                    'status' => $type['status'],
                ]
            );
        }
    }
}
