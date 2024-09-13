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
    
    $this->permission = Permission::factory()->create(['name' => 'role_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of roles', function () {
    $this->actingAs($this->adminUser);
    $roles = Role::factory()->count(3)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($roles as $role) {
        $response->assertJsonFragment([
            'id' => $role->id,
            'name' => $role->name,
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
            'deleted_at' => $role->deleted_at,
        ]);
    }
});

it('should not retrieve a list of roles if unauthorized', function () {
    $this->actingAs($this->user);
    Role::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of roles if unauthenticated', function () {
    Role::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted roles in the list', function () {
    $this->actingAs($this->adminUser);
    Role::factory()->count(3)->create();
    $softDeletedRole = Role::factory()->create();
    $softDeletedRole->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedRole->id]);
});
