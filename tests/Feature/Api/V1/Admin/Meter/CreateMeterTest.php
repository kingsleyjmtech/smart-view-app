<?php

use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meters';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'meter_create']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create meter', function () {
    $this->actingAs($this->adminUser);
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'utility_type_id' => UtilityType::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterData);

    $response->assertStatus(201);
    $response->assertJsonFragment($meterData);
    $this->assertDatabaseHas('meters', $meterData);
});

it('should not create meter if unauthorized', function () {
    $this->actingAs($this->user);
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'utility_type_id' => UtilityType::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('meters', $meterData);
});

it('should not create meter if unauthenticated', function () {
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'utility_type_id' => UtilityType::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('meters', $meterData);
});

it('should return validation errors when creating meter', function () {
    $this->actingAs($this->adminUser);
    $meterData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $meterData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'tenant_id',
        'utility_type_id',
        'code',
        'location',
        'status',
    ]);
});
