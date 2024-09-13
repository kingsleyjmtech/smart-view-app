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
    
    $this->permission = Permission::factory()->create(['name' => 'customer_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete customer', function () {
    $this->actingAs($this->adminUser);
    $customer = Customer::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$customer->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});

it('should not delete customer if unauthorized', function () {
    $this->actingAs($this->user);
    $customer = Customer::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$customer->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('customers', ['id' => $customer->id]);
});

it('should not delete customer if unauthenticated', function () {
    $customer = Customer::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$customer->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('customers', ['id' => $customer->id]);
});
