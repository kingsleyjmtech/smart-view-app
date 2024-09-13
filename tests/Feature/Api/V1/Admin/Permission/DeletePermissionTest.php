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
    
    $this->permission = Permission::factory()->create(['name' => 'permission_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete permission', function () {
    $this->actingAs($this->adminUser);
    $permission = Permission::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$permission->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
});

it('should not delete permission if unauthorized', function () {
    $this->actingAs($this->user);
    $permission = Permission::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$permission->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
});

it('should not delete permission if unauthenticated', function () {
    $permission = Permission::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$permission->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
});
