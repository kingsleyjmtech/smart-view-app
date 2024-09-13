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
    
    $this->permission = Permission::factory()->create(['name' => 'role_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete role', function () {
    $this->actingAs($this->adminUser);
    $role = Role::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$role->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('roles', ['id' => $role->id]);
});

it('should not delete role if unauthorized', function () {
    $this->actingAs($this->user);
    $role = Role::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$role->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});

it('should not delete role if unauthenticated', function () {
    $role = Role::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$role->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});
