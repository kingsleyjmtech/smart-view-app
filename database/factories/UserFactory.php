<?php

namespace Database\Factories;

use App\Models\User;
use DateTimeZone;
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
            'timezone' => fake()->randomElement(DateTimeZone::listIdentifiers()),
            'email_verified_at' => now(tz: config('app.timezone')),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'status' => fake()->randomElement(User::STATUS_SELECT),
        ];
    }
}
