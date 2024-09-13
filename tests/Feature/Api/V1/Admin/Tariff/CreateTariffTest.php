<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/tariffs';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'tariff_create']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create tariff', function () {
    $this->actingAs($this->adminUser);
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tariffData);

    $response->assertStatus(201);
    $response->assertJsonFragment($tariffData);
    $this->assertDatabaseHas('tariffs', $tariffData);
});

it('should not create tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tariffData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tariffs', $tariffData);
});

it('should not create tariff if unauthenticated', function () {
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tariffData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('tariffs', $tariffData);
});

it('should return validation errors when creating tariff', function () {
    $this->actingAs($this->adminUser);
    $tariffData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $tariffData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'rate',
        'description',
        'start_date',
        'name',
    ]);
});
