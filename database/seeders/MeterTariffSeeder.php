<?php

namespace Database\Seeders;

use App\Models\MeterTariff;
use Illuminate\Database\Seeder;

class MeterTariffSeeder extends Seeder
{
    public function run(): void
    {
        MeterTariff::factory()
            ->count(10)
            ->create();
    }
}