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

    $this->permission = Permission::factory()->create(['name' => 'tenant_show']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single tenant', function () {
    $this->actingAs($this->adminUser);
    $tenant = Tenant::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tenant->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $tenant->id,
        'customer_id' => $tenant->customer_id,
        'user_id' => $tenant->user_id,
        'uuid' => $tenant->uuid,
        'status' => $tenant->status,
        'created_at' => $tenant->created_at,
        'updated_at' => $tenant->updated_at,
        'deleted_at' => $tenant->deleted_at,
    ]);
});

it('should not retrieve an tenant if unauthorized', function () {
    $this->actingAs($this->user);
    $tenant = Tenant::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tenant->id}");

    $response->assertStatus(403);
});

it('should not retrieve an tenant if unauthenticated', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tenant->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted tenant', function () {
    $this->actingAs($this->adminUser);
    $tenant = Tenant::factory()->create();
    $tenant->delete();

    $response = $this->getJson("{$this->baseUrl}/{$tenant->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing tenant', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
