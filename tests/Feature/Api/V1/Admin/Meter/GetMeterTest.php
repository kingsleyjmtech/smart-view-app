<?php

use App\Models\Meter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meters';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'meter_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single meter', function () {
    $this->actingAs($this->adminUser);
    $meter = Meter::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meter->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $meter->id,
        'tenant_id' => $meter->tenant_id,
        'user_id' => $meter->user_id,
        'utility_type_id' => $meter->utility_type_id,
        'code' => $meter->code,
        'location' => $meter->location,
        'installation_date' => $meter->installation_date,
        'status' => $meter->status,
        'created_at' => $meter->created_at,
        'updated_at' => $meter->updated_at,
        'deleted_at' => $meter->deleted_at,
    ]);
});

it('should not retrieve an meter if unauthorized', function () {
    $this->actingAs($this->user);
    $meter = Meter::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meter->id}");

    $response->assertStatus(403);
});

it('should not retrieve an meter if unauthenticated', function () {
    $meter = Meter::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meter->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted meter', function () {
    $this->actingAs($this->adminUser);
    $meter = Meter::factory()->create();
    $meter->delete();

    $response = $this->getJson("{$this->baseUrl}/{$meter->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing meter', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
