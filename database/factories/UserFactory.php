<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'timezone' => fake()->sentence(),
            'email_verified_at' => fake()->dateTime(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
