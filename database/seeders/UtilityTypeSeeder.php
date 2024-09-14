<?php

namespace Database\Seeders;

use App\Models\UtilityType;
use Illuminate\Database\Seeder;

class UtilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        UtilityType::factory()
            ->count(10)
            ->create();
    }
}
