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

    $this->permission = Permission::factory()->create(['name' => 'tenant_delete']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete tenant', function () {
    $this->actingAs($this->adminUser);
    $tenant = Tenant::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$tenant->id");

    $response->assertStatus(204);
    $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
});

it('should not delete tenant if unauthorized', function () {
    $this->actingAs($this->user);
    $tenant = Tenant::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$tenant->id");

    $response->assertStatus(403);
    $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
});

it('should not delete tenant if unauthenticated', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$tenant->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
});
