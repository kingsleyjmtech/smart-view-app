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
    
    $this->permission = Permission::factory()->create(['name' => 'user_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete user', function () {
    $this->actingAs($this->adminUser);
    $user = User::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$user->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('should not delete user if unauthorized', function () {
    $this->actingAs($this->user);
    $user = User::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$user->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

it('should not delete user if unauthenticated', function () {
    $user = User::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$user->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});
