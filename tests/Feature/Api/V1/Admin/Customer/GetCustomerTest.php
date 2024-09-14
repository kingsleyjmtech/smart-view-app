<?php

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/customers';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'customer_show']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single customer', function () {
    $this->actingAs($this->adminUser);
    $customer = Customer::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$customer->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $customer->id,
        'user_id' => $customer->user_id,
        'name' => $customer->name,
        'created_at' => $customer->created_at,
        'updated_at' => $customer->updated_at,
        'deleted_at' => $customer->deleted_at,
    ]);
});

it('should not retrieve an customer if unauthorized', function () {
    $this->actingAs($this->user);
    $customer = Customer::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$customer->id}");

    $response->assertStatus(403);
});

it('should not retrieve an customer if unauthenticated', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$customer->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted customer', function () {
    $this->actingAs($this->adminUser);
    $customer = Customer::factory()->create();
    $customer->delete();

    $response = $this->getJson("{$this->baseUrl}/{$customer->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing customer', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
