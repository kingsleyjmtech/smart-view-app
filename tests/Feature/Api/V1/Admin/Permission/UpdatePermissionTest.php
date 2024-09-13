<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/permissions';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'permission_edit']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update permission', function () {
    $this->actingAs($this->adminUser);
    $permission = Permission::factory()->create();
    $permissionData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$permission->id", $permissionData);

    $response->assertStatus(202);
    $response->assertJsonFragment($permissionData);
    $this->assertDatabaseHas('permissions', $permissionData);
});

it('should not update permission if unauthorized', function () {
    $this->actingAs($this->user);
    $permission = Permission::factory()->create();
    $permissionData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$permission->id", $permissionData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('permissions', $permissionData);
});

it('should not update permission if unauthenticated', function () {
    $permission = Permission::factory()->create();
    $permissionData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$permission->id", $permissionData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('permissions', $permissionData);
});

it('should return validation errors when creating permission', function () {
    $this->actingAs($this->adminUser);
    $permission = Permission::factory()->create();
    $permissionData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$permission->id", $permissionData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});

it('should return 404 for a non-existing permission', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
