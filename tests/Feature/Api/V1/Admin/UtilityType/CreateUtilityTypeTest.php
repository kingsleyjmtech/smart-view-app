<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UtilityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/utility-types';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'utility_type_create']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $utilityTypeData);

    $response->assertStatus(201);
    $response->assertJsonFragment($utilityTypeData);
    $this->assertDatabaseHas('utility_types', $utilityTypeData);
});

it('should not create utility type if unauthorized', function () {
    $this->actingAs($this->user);
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $utilityTypeData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('utility_types', $utilityTypeData);
});

it('should not create utility type if unauthenticated', function () {
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", $utilityTypeData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('utility_types', $utilityTypeData);
});

it('should return validation errors when creating utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityTypeData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $utilityTypeData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'status',
    ]);
});
