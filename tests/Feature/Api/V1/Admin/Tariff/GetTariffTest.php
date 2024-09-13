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
    
    $this->permission = Permission::factory()->create(['name' => 'tariff_show']);
    
    $this->adminRole->permissions()->sync([$this->permission->id]);
        
    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();
    
    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single tariff', function () {
    $this->actingAs($this->adminUser);
    $tariff = Tariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tariff->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $tariff->id,
        'rate' => $tariff->rate,
        'description' => $tariff->description,
        'start_date' => $tariff->start_date,
        'end_date' => $tariff->end_date,
        'name' => $tariff->name,
        'created_at' => $tariff->created_at,
        'updated_at' => $tariff->updated_at,
        'deleted_at' => $tariff->deleted_at,
    ]);
});

it('should not retrieve an tariff if unauthorized', function () {
    $this->actingAs($this->user);
    $tariff = Tariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tariff->id}");

    $response->assertStatus(403);
});

it('should not retrieve an tariff if unauthenticated', function () {
    $tariff = Tariff::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$tariff->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted tariff', function () {
    $this->actingAs($this->adminUser);
    $tariff = Tariff::factory()->create();
    $tariff->delete();

    $response = $this->getJson("{$this->baseUrl}/{$tariff->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing tariff', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
