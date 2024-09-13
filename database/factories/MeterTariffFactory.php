<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\MeterTariff;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterTariffFactory extends Factory
{
    protected $model = MeterTariff::class;

    public function definition(): array
    {
        return [
            'meter_id' => Meter::factory(),
            'tariff_id' => Tariff::factory(),
            'effective_from' => fake()->dateTime(),
            'effective_to' => fake()->dateTime(),
        ];
    }
}
