<?php

use App\Models\Consumption;
use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/consumptions';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'consumption_edit']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update consumption', function () {
    $this->actingAs($this->adminUser);
    $consumption = Consumption::factory()->create();
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$consumption->id", $consumptionData);

    $response->assertStatus(202);
    $response->assertJsonFragment($consumptionData);
    $this->assertDatabaseHas('consumptions', $consumptionData);
});

it('should not update consumption if unauthorized', function () {
    $this->actingAs($this->user);
    $consumption = Consumption::factory()->create();
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$consumption->id", $consumptionData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('consumptions', $consumptionData);
});

it('should not update consumption if unauthenticated', function () {
    $consumption = Consumption::factory()->create();
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->putJson("{$this->baseUrl}/$consumption->id", $consumptionData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('consumptions', $consumptionData);
});

it('should return validation errors when creating consumption', function () {
    $this->actingAs($this->adminUser);
    $consumption = Consumption::factory()->create();
    $consumptionData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$consumption->id", $consumptionData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'meter_id',
        'value',
        'date',
    ]);
});

it('should return 404 for a non-existing consumption', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
