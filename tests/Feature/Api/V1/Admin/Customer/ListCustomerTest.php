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

    $this->permission = Permission::factory()->create(['name' => 'customer_access']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of customers', function () {
    $this->actingAs($this->adminUser);
    $customers = Customer::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($customers as $customer) {
        $response->assertJsonFragment([
            'id' => $customer->id,
            'user_id' => $customer->user_id,
            'name' => $customer->name,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,
            'deleted_at' => $customer->deleted_at,
        ]);
    }
});

it('should not retrieve a list of customers if unauthorized', function () {
    $this->actingAs($this->user);
    Customer::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of customers if unauthenticated', function () {
    Customer::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted customers in the list', function () {
    $this->actingAs($this->adminUser);
    Customer::factory()->count(5)->create();
    $softDeletedCustomer = Customer::factory()->create();
    $softDeletedCustomer->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedCustomer->id]);
});
