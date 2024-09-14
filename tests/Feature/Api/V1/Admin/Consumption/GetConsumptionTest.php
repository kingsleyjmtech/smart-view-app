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

    $this->permission = Permission::factory()->create(['name' => 'consumption_show']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a single consumption', function () {
    $this->actingAs($this->adminUser);
    $consumption = Consumption::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$consumption->id}");

    $response->assertStatus(200);
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
});

it('should not retrieve an consumption if unauthorized', function () {
    $this->actingAs($this->user);
    $consumption = Consumption::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$consumption->id}");

    $response->assertStatus(403);
});

it('should not retrieve an consumption if unauthenticated', function () {
    $consumption = Consumption::factory()->create();

    $response = $this->getJson("{$this->baseUrl}/{$consumption->id}");

    $response->assertStatus(401);
});

it('should not retrieve a soft deleted consumption', function () {
    $this->actingAs($this->adminUser);
    $consumption = Consumption::factory()->create();
    $consumption->delete();

    $response = $this->getJson("{$this->baseUrl}/{$consumption->id}");

    $response->assertStatus(404);
});

it('should return 404 for a non-existing consumption', function () {
    $this->actingAs($this->adminUser);
    $nonExistingId = 999;

    $response = $this->getJson("{$this->baseUrl}/{$nonExistingId}");

    $response->assertStatus(404);
});
