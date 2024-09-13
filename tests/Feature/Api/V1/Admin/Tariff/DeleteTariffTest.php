<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1/admin/tariffs';
    $this->adminRole = Role::factory()->create(['name' => 'Admin']);
    $this->userRole = Role::factory()->create(['name' => 'User']);
    
    $this->permission = Permission::factory()->create(['name' => 'tariff_delete']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete tariff', function () {
    $this->actingAs($this->adminUser);
    $tariff = Tariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$tariff->id");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('tariffs', ['id' => $tariff->id]);
});

it('should not delete tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $tariff = Tariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$tariff->id");
    
    $response->assertStatus(403);
    $this->assertDatabaseHas('tariffs', ['id' => $tariff->id]);
});

it('should not delete tariff if unauthenticated', function () {
    $tariff = Tariff::factory()->create();
    
    $response = $this->deleteJson("/$this->baseUrl/$tariff->id");
    
    $response->assertStatus(401);
    $this->assertDatabaseHas('tariffs', ['id' => $tariff->id]);
});
