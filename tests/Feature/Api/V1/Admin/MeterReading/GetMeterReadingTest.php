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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_reading_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single meter reading', function () {
    $this->actingAs($this->adminUser);
    $meterReading = MeterReading::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterReading->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $meterReading->id,
        'meter_id' => $meterReading->meter_id,
        'reading_date' => $meterReading->reading_date->format('Y-m-d H:i:s'),
        'value' => $meterReading->value,
        'source' => $meterReading->source,
        'created_at' => $meterReading->created_at,
        'updated_at' => $meterReading->updated_at,
        'deleted_at' => $meterReading->deleted_at,
    ]);
});

it('should not retrieve an meter reading if unauthorized', function () {
    $this->actingAs($this->user);
    $meterReading = MeterReading::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterReading->id}");

    $response->assertStatus(403);
});

it('should not retrieve an meter reading if unauthenticated', function () {
    $meterReading = MeterReading::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterReading->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted meter reading', function () {
    $this->actingAs($this->adminUser);
    $meterReading = MeterReading::factory()->create();
    $meterReading->delete();

    $response = $this->getJson("{$this->baseUrl}/{$meterReading->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing meter reading', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
