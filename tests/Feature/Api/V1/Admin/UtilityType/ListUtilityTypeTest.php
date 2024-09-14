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
    
    $this->permission = Permission::factory()->create(['name' => 'utility_type_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of utility types', function () {
    $this->actingAs($this->adminUser);
    $utilityTypes = UtilityType::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($utilityTypes as $utilityType) {
        $response->assertJsonFragment([
            'id' => $utilityType->id,
            'name' => $utilityType->name,
            'description' => $utilityType->description,
            'created_at' => $utilityType->created_at,
            'updated_at' => $utilityType->updated_at,
            'deleted_at' => $utilityType->deleted_at,
        ]);
    }
});

it('should not retrieve a list of utility types if unauthorized', function () {
    $this->actingAs($this->user);
    UtilityType::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of utility types if unauthenticated', function () {
    UtilityType::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted utility types in the list', function () {
    $this->actingAs($this->adminUser);
    UtilityType::factory()->count(5)->create();
    $softDeletedUtilityType = UtilityType::factory()->create();
    $softDeletedUtilityType->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedUtilityType->id]);
});
