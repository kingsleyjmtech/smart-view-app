<?php

namespace Database\Factories;

use App\Models\Consumption;
use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumptionFactory extends Factory
{
    protected $model = Consumption::class;

    public function definition(): array
    {
        return [
            'meter_id' => Meter::factory(),
            'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
            'value' => fake()->numberBetween(10, 10000),
            'date' => fake()->dateTime(),
        ];
    }
}
