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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_tariff_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete meter tariff', function () {
    $this->actingAs($this->adminUser);
    $meterTariff = MeterTariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterTariff->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('meter_tariffs', ['id' => $meterTariff->id]);
});

it('should not delete meter tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $meterTariff = MeterTariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterTariff->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('meter_tariffs', ['id' => $meterTariff->id]);
});

it('should not delete meter tariff if unauthenticated', function () {
    $meterTariff = MeterTariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterTariff->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('meter_tariffs', ['id' => $meterTariff->id]);
});
