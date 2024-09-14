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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of meters', function () {
    $this->actingAs($this->adminUser);
    $meters = Meter::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($meters as $meter) {
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
    }
});

it('should not retrieve a list of meters if unauthorized', function () {
    $this->actingAs($this->user);
    Meter::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of meters if unauthenticated', function () {
    Meter::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted meters in the list', function () {
    $this->actingAs($this->adminUser);
    Meter::factory()->count(5)->create();
    $softDeletedMeter = Meter::factory()->create();
    $softDeletedMeter->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedMeter->id]);
});
