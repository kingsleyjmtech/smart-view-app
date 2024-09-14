<?php

namespace Database\Seeders;

use App\Models\MeterReading;
use Illuminate\Database\Seeder;

class MeterReadingSeeder extends Seeder
{
    public function run(): void
    {
        MeterReading::factory()
            ->count(10)
            ->create();
    }
}
