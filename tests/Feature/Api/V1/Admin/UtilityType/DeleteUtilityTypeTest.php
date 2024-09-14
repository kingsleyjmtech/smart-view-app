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
    
    $this->permission = Permission::factory()->create(['name' => 'utility_type_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete utility type', function () {
    $this->actingAs($this->adminUser);
    $utilityType = UtilityType::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$utilityType->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('utility_types', ['id' => $utilityType->id]);
});

it('should not delete utility type if unauthorized', function () {
    $this->actingAs($this->user);
    $utilityType = UtilityType::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$utilityType->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('utility_types', ['id' => $utilityType->id]);
});

it('should not delete utility type if unauthenticated', function () {
    $utilityType = UtilityType::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$utilityType->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('utility_types', ['id' => $utilityType->id]);
});
