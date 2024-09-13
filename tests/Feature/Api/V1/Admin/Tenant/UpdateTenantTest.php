<?php

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/tenants';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'tenant_edit']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update tenant', function () {
    $this->actingAs($this->adminUser);
    $tenant = Tenant::factory()->create();
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tenant->id", $tenantData);

    $response->assertStatus(202);
    $response->assertJsonFragment($tenantData);
    $this->assertDatabaseHas('tenants', $tenantData);
});

it('should not update tenant if unauthorized', function () {
    $this->actingAs($this->user);
    $tenant = Tenant::factory()->create();
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tenant->id", $tenantData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tenants', $tenantData);
});

it('should not update tenant if unauthenticated', function () {
    $tenant = Tenant::factory()->create();
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$tenant->id", $tenantData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('tenants', $tenantData);
});

it('should return validation errors when creating tenant', function () {
    $this->actingAs($this->adminUser);
    $tenant = Tenant::factory()->create();
    $tenantData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$tenant->id", $tenantData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'customer_id',
    ]);
});

it('should return 404 for a non-existing tenant', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
