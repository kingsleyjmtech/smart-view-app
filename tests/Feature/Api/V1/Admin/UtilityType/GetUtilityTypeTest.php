<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UtilityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/utility-types';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'utility_type_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityType = UtilityType::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$utilityType->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $utilityType->id,
        'name' => $utilityType->name,
        'description' => $utilityType->description,
        'created_at' => $utilityType->created_at,
        'updated_at' => $utilityType->updated_at,
        'deleted_at' => $utilityType->deleted_at,
    ]);
});

it('should not retrieve an utility type if unauthorized', function () {
    $this->actingAs($this->user);
    $utilityType = UtilityType::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$utilityType->id}");

    $response->assertStatus(403);
});

it('should not retrieve an utility type if unauthenticated', function () {
    $utilityType = UtilityType::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$utilityType->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityType = UtilityType::factory()->create();
    $utilityType->delete();

    $response = $this->getJson("{$this->baseUrl}/{$utilityType->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing utility type', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
