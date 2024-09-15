<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/users';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'user_show']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single user', function () {
    $this->actingAs($this->adminUser);
    $user = User::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$user->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'timezone' => $user->timezone,
        'email_verified_at' => $user->email_verified_at->format('Y-m-d H:i:s'),
        'status' => $user->status,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'deleted_at' => $user->deleted_at,
    ]);
});

it('should not retrieve an user if unauthorized', function () {
    $this->actingAs($this->user);
    $user = User::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$user->id}");

    $response->assertStatus(403);
});

it('should not retrieve an user if unauthenticated', function () {
    $user = User::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$user->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted user', function () {
    $this->actingAs($this->adminUser);
    $user = User::factory()->create();
    $user->delete();

    $response = $this->getJson("{$this->baseUrl}/{$user->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing user', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
