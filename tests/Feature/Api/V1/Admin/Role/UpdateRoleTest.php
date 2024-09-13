<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/roles';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'role_edit']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update role', function () {
    $this->actingAs($this->adminUser);
    $role = Role::factory()->create();
    $roleReqData = [
        'permissions' => [Permission::factory()->create()->id],
    ];
    $roleData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$role->id", array_merge($roleReqData, $roleData));

    $response->assertStatus(202);
    $response->assertJsonFragment($roleData);
    $this->assertDatabaseHas('roles', $roleData);
});

it('should not update role if unauthorized', function () {
    $this->actingAs($this->user);
    $role = Role::factory()->create();
    $roleReqData = [
        'permissions' => [Permission::factory()->create()->id],
    ];
    $roleData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$role->id", array_merge($roleReqData, $roleData));

    $response->assertStatus(403);
    $this->assertDatabaseMissing('roles', $roleData);
});

it('should not update role if unauthenticated', function () {
    $role = Role::factory()->create();
    $roleReqData = [
        'permissions' => [Permission::factory()->create()->id],
    ];
    $roleData = [
        'name' => trim(Str::substr(fake()->name(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$role->id", array_merge($roleReqData, $roleData));

    $response->assertStatus(401);
    $this->assertDatabaseMissing('roles', $roleData);
});

it('should return validation errors when creating role', function () {
    $this->actingAs($this->adminUser);
    $role = Role::factory()->create();
    $roleData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$role->id", $roleData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});

it('should return 404 for a non-existing role', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
