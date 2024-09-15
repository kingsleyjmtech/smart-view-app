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

    $this->permission = Permission::factory()->create(['name' => 'utility_type_edit']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityType = UtilityType::factory()->create();
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$utilityType->id", $utilityTypeData);

    $response->assertStatus(202);
    $response->assertJsonFragment($utilityTypeData);
    $this->assertDatabaseHas('utility_types', $utilityTypeData);
});

it('should not update utility type if unauthorized', function () {
    $this->actingAs($this->user);
    $utilityType = UtilityType::factory()->create();
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$utilityType->id", $utilityTypeData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('utility_types', $utilityTypeData);
});

it('should not update utility type if unauthenticated', function () {
    $utilityType = UtilityType::factory()->create();
    $utilityTypeData = [
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
        'description' => fake()->sentence(20),
        'status' => fake()->randomElement(UtilityType::STATUS_SELECT),
    ];

    $response = $this->putJson("{$this->baseUrl}/$utilityType->id", $utilityTypeData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('utility_types', $utilityTypeData);
});

it('should return validation errors when creating utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityType = UtilityType::factory()->create();
    $utilityTypeData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$utilityType->id", $utilityTypeData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'status',
    ]);
});

it('should return 404 for a non-existing utility type', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
