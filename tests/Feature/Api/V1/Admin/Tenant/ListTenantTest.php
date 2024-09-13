<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/tenants';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'tenant_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of tenants', function () {
    $this->actingAs($this->adminUser);
    $tenants = Tenant::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($tenants as $tenant) {
        $response->assertJsonFragment([
            'id' => $tenant->id,
            'customer_id' => $tenant->customer_id,
            'user_id' => $tenant->user_id,
            'uuid' => $tenant->uuid,
            'created_at' => $tenant->created_at,
            'updated_at' => $tenant->updated_at,
            'deleted_at' => $tenant->deleted_at,
        ]);
    }
});

it('should not retrieve a list of tenants if unauthorized', function () {
    $this->actingAs($this->user);
    Tenant::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of tenants if unauthenticated', function () {
    Tenant::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted tenants in the list', function () {
    $this->actingAs($this->adminUser);
    Tenant::factory()->count(5)->create();
    $softDeletedTenant = Tenant::factory()->create();
    $softDeletedTenant->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedTenant->id]);
});
