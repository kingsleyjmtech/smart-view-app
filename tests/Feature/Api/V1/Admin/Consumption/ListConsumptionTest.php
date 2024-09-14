<?php

use App\Models\Consumption;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/consumptions';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'consumption_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of consumptions', function () {
    $this->actingAs($this->adminUser);
    $consumptions = Consumption::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($consumptions as $consumption) {
        $response->assertJsonFragment([
            'id' => $consumption->id,
            'meter_id' => $consumption->meter_id,
            'aggregation_period' => $consumption->aggregation_period,
            'value' => $consumption->value,
            'date' => $consumption->date->format('Y-m-d H:i:s'),
            'created_at' => $consumption->created_at,
            'updated_at' => $consumption->updated_at,
            'deleted_at' => $consumption->deleted_at,
        ]);
    }
});

it('should not retrieve a list of consumptions if unauthorized', function () {
    $this->actingAs($this->user);
    Consumption::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of consumptions if unauthenticated', function () {
    Consumption::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted consumptions in the list', function () {
    $this->actingAs($this->adminUser);
    Consumption::factory()->count(5)->create();
    $softDeletedConsumption = Consumption::factory()->create();
    $softDeletedConsumption->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedConsumption->id]);
});
