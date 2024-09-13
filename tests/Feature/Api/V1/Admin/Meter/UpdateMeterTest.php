<?php

use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meters';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'meter_edit']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update meter', function () {
    $this->actingAs($this->adminUser);
    $meter = Meter::factory()->create();
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meter->id", $meterData);

    $response->assertStatus(202);
    $response->assertJsonFragment($meterData);
    $this->assertDatabaseHas('meters', $meterData);
});

it('should not update meter if unauthorized', function () {
    $this->actingAs($this->user);
    $meter = Meter::factory()->create();
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meter->id", $meterData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('meters', $meterData);
});

it('should not update meter if unauthenticated', function () {
    $meter = Meter::factory()->create();
    $meterData = [
        'tenant_id' => Tenant::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'code' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'location' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'installation_date' => fake()->date(),
        'status' => fake()->randomElement(Meter::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$meter->id", $meterData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('meters', $meterData);
});

it('should return validation errors when creating meter', function () {
    $this->actingAs($this->adminUser);
    $meter = Meter::factory()->create();
    $meterData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$meter->id", $meterData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'tenant_id',
        'code',
        'location',
        'status',
    ]);
});

it('should return 404 for a non-existing meter', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
