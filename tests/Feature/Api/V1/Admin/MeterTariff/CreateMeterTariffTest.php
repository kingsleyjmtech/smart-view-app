<?php

use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meter-tariffs';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'meter_tariff_create']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterTariffData);

    $response->assertStatus(201);
    $response->assertJsonFragment($meterTariffData);
    $this->assertDatabaseHas('meter_tariffs', $meterTariffData);
});

it('should not create meter tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterTariffData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('meter_tariffs', $meterTariffData);
});

it('should not create meter tariff if unauthenticated', function () {
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterTariffData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('meter_tariffs', $meterTariffData);
});

it('should return validation errors when creating meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariffData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterTariffData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'meter_id',
        'tariff_id',
        'effective_from',
    ]);
});
