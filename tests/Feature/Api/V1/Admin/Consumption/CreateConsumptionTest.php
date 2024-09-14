<?php

use App\Models\Consumption;
use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/consumptions';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'consumption_create']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create consumption', function () {
    $this->actingAs($this->adminUser);
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $consumptionData);

    $response->assertStatus(201);
    $response->assertJsonFragment($consumptionData);
    $this->assertDatabaseHas('consumptions', $consumptionData);
});

it('should not create consumption if unauthorized', function () {
    $this->actingAs($this->user);
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $consumptionData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('consumptions', $consumptionData);
});

it('should not create consumption if unauthenticated', function () {
    $consumptionData = [
        'meter_id' => Meter::factory()->create()->id,
        'aggregation_period' => fake()->randomElement(Consumption::AGGREGATION_PERIOD_SELECT),
        'value' => fake()->numberBetween(10, 10000),
        'date' => fake()->dateTime()->format('Y-m-d H:i:s'),
    ];

    $response = $this->postJson("{$this->baseUrl}", $consumptionData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('consumptions', $consumptionData);
});

it('should return validation errors when creating consumption', function () {
    $this->actingAs($this->adminUser);
    $consumptionData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $consumptionData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'meter_id',
        'value',
        'date',
    ]);
});
