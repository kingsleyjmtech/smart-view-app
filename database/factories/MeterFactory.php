<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterFactory extends Factory
{
    protected $model = Meter::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'code' => fake()->sentence(),
            'location' => fake()->sentence(),
            'installation_date' => fake()->date(),
            'status' => fake()->randomElement(Meter::STATUS_SELECT),
        ];
    }
}
