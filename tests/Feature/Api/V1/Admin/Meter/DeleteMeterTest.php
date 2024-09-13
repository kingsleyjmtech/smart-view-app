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
    
    $this->permission = Permission::factory()->create(['name' => 'meter_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete meter', function () {
    $this->actingAs($this->adminUser);
    $meter = Meter::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meter->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('meters', ['id' => $meter->id]);
});

it('should not delete meter if unauthorized', function () {
    $this->actingAs($this->user);
    $meter = Meter::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meter->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('meters', ['id' => $meter->id]);
});

it('should not delete meter if unauthenticated', function () {
    $meter = Meter::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$meter->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('meters', ['id' => $meter->id]);
});
