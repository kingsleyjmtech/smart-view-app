<?php

namespace Database\Factories;

use App\Models\UtilityType;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtilityTypeFactory extends Factory
{
    protected $model = UtilityType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->sentence(20),
        ];
    }
}
