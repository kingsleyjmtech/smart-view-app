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
    
    $this->permission = Permission::factory()->create(['name' => 'tenant_create']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should create tenant', function () {
    $this->actingAs($this->adminUser);
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tenantData);

    $response->assertStatus(201);
    $response->assertJsonFragment($tenantData);
    $this->assertDatabaseHas('tenants', $tenantData);
});

it('should not create tenant if unauthorized', function () {
    $this->actingAs($this->user);
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tenantData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tenants', $tenantData);
});

it('should not create tenant if unauthenticated', function () {
    $tenantData = [
        'customer_id' => Customer::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'uuid' => trim(Str::substr(fake()->sentence(), 1, 255)),
    ];

    $response = $this->postJson("{$this->baseUrl}", $tenantData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('tenants', $tenantData);
});

it('should return validation errors when creating tenant', function () {
    $this->actingAs($this->adminUser);
    $tenantData = [
        // Add invalid data here
    ];

    $response = $this->postJson("{$this->baseUrl}", $tenantData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'customer_id',
    ]);
});
