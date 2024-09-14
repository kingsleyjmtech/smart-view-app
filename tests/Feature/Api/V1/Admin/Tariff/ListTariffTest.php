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

    $this->permission = Permission::factory()->create(['name' => 'tariff_access']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should retrieve a list of tariffs', function () {
    $this->actingAs($this->adminUser);
    $tariffs = Tariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    foreach ($tariffs as $tariff) {
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
    }
});

it('should not retrieve a list of tariffs if unauthorized', function () {
    $this->actingAs($this->user);
    Tariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(403);
});

it('should not retrieve a list of tariffs if unauthenticated', function () {
    Tariff::factory()->count(5)->create();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(401);
});

it('should not include soft deleted tariffs in the list', function () {
    $this->actingAs($this->adminUser);
    Tariff::factory()->count(5)->create();
    $softDeletedTariff = Tariff::factory()->create();
    $softDeletedTariff->delete();

    $response = $this->getJson("{$this->baseUrl}");

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
    $response->assertJsonMissing(['id' => $softDeletedTariff->id]);
});
