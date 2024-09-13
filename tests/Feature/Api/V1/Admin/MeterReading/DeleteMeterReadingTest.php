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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_reading_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete meter reading', function () {
    $this->actingAs($this->adminUser);
    $meterReading = MeterReading::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterReading->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('meter_readings', ['id' => $meterReading->id]);
});

it('should not delete meter reading if unauthorized', function () {
    $this->actingAs($this->user);
    $meterReading = MeterReading::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterReading->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('meter_readings', ['id' => $meterReading->id]);
});

it('should not delete meter reading if unauthenticated', function () {
    $meterReading = MeterReading::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meterReading->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('meter_readings', ['id' => $meterReading->id]);
});
