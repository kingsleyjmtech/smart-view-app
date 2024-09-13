<?php

use App\Models\MeterReading;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/meter-readings';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'meter_reading_access']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of meter readings', function () {
    $this->actingAs($this->adminUser);
    $meterReadings = MeterReading::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($meterReadings as $meterReading) {
        $response->assertJsonFragment([
            'id' => $meterReading->id,
            'meter_id' => $meterReading->meter_id,
            'reading_date' => $meterReading->reading_date->format('Y-m-d H:i:s'),
            'value' => $meterReading->value,
            'created_at' => $meterReading->created_at,
            'updated_at' => $meterReading->updated_at,
            'deleted_at' => $meterReading->deleted_at,
        ]);
    }
});

it('should not retrieve a list of meter readings if unauthorized', function () {
    $this->actingAs($this->user);
    MeterReading::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of meter readings if unauthenticated', function () {
    MeterReading::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted meter readings in the list', function () {
    $this->actingAs($this->adminUser);
    MeterReading::factory()->count(5)->create();
    $softDeletedMeterReading = MeterReading::factory()->create();
    $softDeletedMeterReading->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedMeterReading->id]);
});
