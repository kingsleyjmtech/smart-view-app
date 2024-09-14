<?php

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/customers';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);

    $this->permission = Permission::factory()->create(['name' => 'customer_edit']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should update customer', function () {
    $this->actingAs($this->adminUser);
    $customer = Customer::factory()->create();
    $customerData = [
        'user_id' => User::factory()->create()->id,
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$customer->id", $customerData);

    $response->assertStatus(202);
    $response->assertJsonFragment($customerData);
    $this->assertDatabaseHas('customers', $customerData);
});

it('should not update customer if unauthorized', function () {
    $this->actingAs($this->user);
    $customer = Customer::factory()->create();
    $customerData = [
        'user_id' => User::factory()->create()->id,
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$customer->id", $customerData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('customers', $customerData);
});

it('should not update customer if unauthenticated', function () {
    $customer = Customer::factory()->create();
    $customerData = [
        'user_id' => User::factory()->create()->id,
        'name' => trim(Str::substr(fake()->name(), 2, 100)),
    ];

    $response = $this->putJson("{$this->baseUrl}/$customer->id", $customerData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('customers', $customerData);
});

it('should return validation errors when creating customer', function () {
    $this->actingAs($this->adminUser);
    $customer = Customer::factory()->create();
    $customerData = [
        // Add invalid data here
    ];

    $response = $this->putJson("{$this->baseUrl}/$customer->id", $customerData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'user_id',
        'name',
    ]);
});

it('should return 404 for a non-existing customer', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->putJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
