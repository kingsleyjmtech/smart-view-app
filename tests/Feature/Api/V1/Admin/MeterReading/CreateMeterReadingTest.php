<?php

use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meter-readings';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'meter_reading_create']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create meter reading', function () {
    $this->actingAs($this->adminUser);
    $meterReadingData = [
        'meter_id' => Meter::factory()->create()->id,
        'reading_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'value' => fake()->numberBetween(10, 10000),
        'source' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterReadingData);

    $response->assertStatus(201);
    $response->assertJsonFragment($meterReadingData);
    $this->assertDatabaseHas('meter_readings', $meterReadingData);
});

it('should not create meter reading if unauthorized', function () {
    $this->actingAs($this->user);
    $meterReadingData = [
        'meter_id' => Meter::factory()->create()->id,
        'reading_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'value' => fake()->numberBetween(10, 10000),
        'source' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterReadingData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('meter_readings', $meterReadingData);
});

it('should not create meter reading if unauthenticated', function () {
    $meterReadingData = [
        'meter_id' => Meter::factory()->create()->id,
        'reading_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'value' => fake()->numberBetween(10, 10000),
        'source' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterReadingData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('meter_readings', $meterReadingData);
});

it('should return validation errors when creating meter reading', function () {
    $this->actingAs($this->adminUser);
    $meterReadingData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterReadingData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'meter_id',
        'reading_date',
        'value',
        'source',
    ]);
});
