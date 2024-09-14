<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/permissions';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'permission_access']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of permissions', function () {
    $this->actingAs($this->adminUser);
    $permissions = Permission::factory()->count(4)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($permissions as $permission) {
        $response->assertJsonFragment([
            'id' => $permission->id,
            'name' => $permission->name,
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
            'deleted_at' => $permission->deleted_at,
        ]);
    }
});

it('should not retrieve a list of permissions if unauthorized', function () {
    $this->actingAs($this->user);
    Permission::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of permissions if unauthenticated', function () {
    Permission::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted permissions in the list', function () {
    $this->actingAs($this->adminUser);
    Permission::factory()->count(4)->create();
    $softDeletedPermission = Permission::factory()->create();
    $softDeletedPermission->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedPermission->id]);
});
