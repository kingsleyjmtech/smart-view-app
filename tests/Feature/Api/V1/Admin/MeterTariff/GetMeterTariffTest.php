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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_tariff_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariff = MeterTariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterTariff->id}");

    $response->assertStatus(200);
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
});

it('should not retrieve an meter tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $meterTariff = MeterTariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterTariff->id}");

    $response->assertStatus(403);
});

it('should not retrieve an meter tariff if unauthenticated', function () {
    $meterTariff = MeterTariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$meterTariff->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariff = MeterTariff::factory()->create();
    $meterTariff->delete();

    $response = $this->getJson("{$this->baseUrl}/{$meterTariff->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing meter tariff', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
