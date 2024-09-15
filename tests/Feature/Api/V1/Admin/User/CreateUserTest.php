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

    $this->permission = Permission::factory()->create(['name' => 'user_create']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create user', function () {
    $this->actingAs($this->adminUser);
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
        'status' => fake()->randomElement(User::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", array_merge($userReqData, $userData));

    $response->assertStatus(201);
    unset($userData['password']);
    $response->assertJsonFragment($userData);
    $this->assertDatabaseHas('users', $userData);
});

it('should not create user if unauthorized', function () {
    $this->actingAs($this->user);
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
        'status' => fake()->randomElement(User::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", array_merge($userReqData, $userData));

    $response->assertStatus(403);
    unset($userData['password']);
    $this->assertDatabaseMissing('users', $userData);
});

it('should not create user if unauthenticated', function () {
    $userReqData = [
        'roles' => [Role::factory()->create()->id],
    ];
    $userData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
        'email' => fake()->unique()->safeEmail(),
        'timezone' => trim(Str::substr(fake()->sentence(), 1, 255)),
        'email_verified_at' => fake()->dateTime()->format('Y-m-d H:i:s'),
        'password' => 'password',
        'status' => fake()->randomElement(User::STATUS_SELECT),
    ];

    $response = $this->postJson("{$this->baseUrl}", array_merge($userReqData, $userData));

    $response->assertStatus(401);
    unset($userData['password']);
    $this->assertDatabaseMissing('users', $userData);
});

it('should return validation errors when creating user', function () {
    $this->actingAs($this->adminUser);
    $userData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $userData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'email',
        'password',
        'status',
    ]);
});
