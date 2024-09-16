<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Meter;
use App\Models\Tenant;
use App\Models\UtilityType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterFactory extends Factory
{
    protected $model = Meter::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => Customer::factory(),
            'utility_type_id' => UtilityType::factory(),
            'location' => fake()->address(),
            'installation_date' => fake()->date(),
            'status' => fake()->randomElement(Meter::STATUS_SELECT),
        ];
    }
}
