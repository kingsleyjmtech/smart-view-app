<?php

use App\Models\Meter;
use App\Models\MeterTariff;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meter-tariffs';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'meter_tariff_edit']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariff = MeterTariff::factory()->create();
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meterTariff->id", $meterTariffData);

    $response->assertStatus(202);
    $response->assertJsonFragment($meterTariffData);
    $this->assertDatabaseHas('meter_tariffs', $meterTariffData);
});

it('should not update meter tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $meterTariff = MeterTariff::factory()->create();
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meterTariff->id", $meterTariffData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('meter_tariffs', $meterTariffData);
});

it('should not update meter tariff if unauthenticated', function () {
    $meterTariff = MeterTariff::factory()->create();
    $meterTariffData = [
        'meter_id' => Meter::factory()->create()->id,
        'tariff_id' => Tariff::factory()->create()->id,
        'effective_from' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'effective_to' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meterTariff->id", $meterTariffData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('meter_tariffs', $meterTariffData);
});

it('should return validation errors when creating meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariff = MeterTariff::factory()->create();
    $meterTariffData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$meterTariff->id", $meterTariffData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'meter_id',
        'tariff_id',
        'effective_from',
    ]);
});

it('should return 404 for a non-existing meter tariff', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
