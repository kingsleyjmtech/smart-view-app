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
    
    $this->permission = Permission::factory()->create(['name' => 'permission_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single permission', function () {
    $this->actingAs($this->adminUser);
    $permission = Permission::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$permission->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $permission->id,
        'name' => $permission->name,
        'created_at' => $permission->created_at,
        'updated_at' => $permission->updated_at,
        'deleted_at' => $permission->deleted_at,
    ]);
});

it('should not retrieve an permission if unauthorized', function () {
    $this->actingAs($this->user);
    $permission = Permission::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$permission->id}");

    $response->assertStatus(403);
});

it('should not retrieve an permission if unauthenticated', function () {
    $permission = Permission::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$permission->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted permission', function () {
    $this->actingAs($this->adminUser);
    $permission = Permission::factory()->create();
    $permission->delete();

    $response = $this->getJson("{$this->baseUrl}/{$permission->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing permission', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
