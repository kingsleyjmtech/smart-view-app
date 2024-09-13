<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/roles';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'role_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single role', function () {
    $this->actingAs($this->adminUser);
    $role = Role::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$role->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $role->id,
        'name' => $role->name,
        'created_at' => $role->created_at,
        'updated_at' => $role->updated_at,
        'deleted_at' => $role->deleted_at,
    ]);
});

it('should not retrieve an role if unauthorized', function () {
    $this->actingAs($this->user);
    $role = Role::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$role->id}");

    $response->assertStatus(403);
});

it('should not retrieve an role if unauthenticated', function () {
    $role = Role::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$role->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted role', function () {
    $this->actingAs($this->adminUser);
    $role = Role::factory()->create();
    $role->delete();

    $response = $this->getJson("{$this->baseUrl}/{$role->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing role', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
