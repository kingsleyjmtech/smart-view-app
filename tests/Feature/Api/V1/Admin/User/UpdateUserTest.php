<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/users';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'user_edit']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update user', function () {
    $this->actingAs($this->adminUser);
    $user = User::factory()->create();
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
    ];

    $response = $this->putJson("{$this->baseUrl}/$user->id", array_merge($userReqData, $userData));

    $response->assertStatus(202);
    unset($userData['password']);
    $response->assertJsonFragment($userData);
    $this->assertDatabaseHas('users', $userData);
});

it('should not update user if unauthorized', function () {
    $this->actingAs($this->user);
    $user = User::factory()->create();
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
    ];

    $response = $this->putJson("{$this->baseUrl}/$user->id", array_merge($userReqData, $userData));

    $response->assertStatus(403);
    unset($userData['password']);
    $this->assertDatabaseMissing('users', $userData);
});

it('should not update user if unauthenticated', function () {
    $user = User::factory()->create();
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
    ];

    $response = $this->putJson("{$this->baseUrl}/$user->id", array_merge($userReqData, $userData));

    $response->assertStatus(401);
    unset($userData['password']);
    $this->assertDatabaseMissing('users', $userData);
});

it('should return validation errors when creating user', function () {
    $this->actingAs($this->adminUser);
    $user = User::factory()->create();
    $userData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$user->id", $userData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'email',
        'password',
    ]);
});

it('should return 404 for a non-existing user', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
