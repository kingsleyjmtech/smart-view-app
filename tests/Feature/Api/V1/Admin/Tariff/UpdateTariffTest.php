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

    $this->permission = Permission::factory()->create(['name' => 'tariff_edit']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update tariff', function () {
    $this->actingAs($this->adminUser);
    $tariff = Tariff::factory()->create();
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tariff->id", $tariffData);

    $response->assertStatus(202);
    $response->assertJsonFragment($tariffData);
    $this->assertDatabaseHas('tariffs', $tariffData);
});

it('should not update tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $tariff = Tariff::factory()->create();
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tariff->id", $tariffData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tariffs', $tariffData);
});

it('should not update tariff if unauthenticated', function () {
    $tariff = Tariff::factory()->create();
    $tariffData = [
        'rate' => fake()->numberBetween(10, 10000),
        'description' => fake()->sentence(20),
        'start_date' => fake()->date(),
        'end_date' => fake()->date(),
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tariff->id", $tariffData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('tariffs', $tariffData);
});

it('should return validation errors when creating tariff', function () {
    $this->actingAs($this->adminUser);
    $tariff = Tariff::factory()->create();
    $tariffData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$tariff->id", $tariffData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'rate',
        'description',
        'start_date',
        'name',
    ]);
});

it('should return 404 for a non-existing tariff', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
