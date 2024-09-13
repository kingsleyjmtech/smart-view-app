<?php

namespace Database\Factories;

use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

class TariffFactory extends Factory
{
    protected $model = Tariff::class;

    public function definition(): array
    {
        return [
            'rate' => fake()->numberBetween(10, 10000),
            'description' => fake()->sentence(20),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'name' => fake()->name(),
        ];
    }
}
