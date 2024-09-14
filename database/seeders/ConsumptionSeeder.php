<?php

namespace Database\Seeders;

use App\Models\Consumption;
use Illuminate\Database\Seeder;

class ConsumptionSeeder extends Seeder
{
    public function run(): void
    {
        Consumption::factory()
            ->count(10)
            ->create();
    }
}
