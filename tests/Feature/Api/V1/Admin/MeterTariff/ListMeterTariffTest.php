<?php

use App\Models\MeterTariff;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meter-tariffs';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'meter_tariff_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of meter tariffs', function () {
    $this->actingAs($this->adminUser);
    $meterTariffs = MeterTariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($meterTariffs as $meterTariff) {
        $response->assertJsonFragment([
            'id' => $meterTariff->id,
            'meter_id' => $meterTariff->meter_id,
            'tariff_id' => $meterTariff->tariff_id,
            'effective_from' => $meterTariff->effective_from->format('Y-m-d H:i:s'),
            'effective_to' => $meterTariff->effective_to->format('Y-m-d H:i:s'),
            'created_at' => $meterTariff->created_at,
            'updated_at' => $meterTariff->updated_at,
            'deleted_at' => $meterTariff->deleted_at,
        ]);
    }
});

it('should not retrieve a list of meter tariffs if unauthorized', function () {
    $this->actingAs($this->user);
    MeterTariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of meter tariffs if unauthenticated', function () {
    MeterTariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted meter tariffs in the list', function () {
    $this->actingAs($this->adminUser);
    MeterTariff::factory()->count(5)->create();
    $softDeletedMeterTariff = MeterTariff::factory()->create();
    $softDeletedMeterTariff->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedMeterTariff->id]);
});
