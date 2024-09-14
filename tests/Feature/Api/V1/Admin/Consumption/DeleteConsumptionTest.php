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

    $this->permission = Permission::factory()->create(['name' => 'consumption_delete']);

    $this->adminRole->permissions()->sync([$this->permission->id]);

    $this->adminUser = User::factory()->create();
    $this->user = User::factory()->create();

    $this->adminUser->roles()->sync([$this->adminRole->id]);
});

it('should delete consumption', function () {
    $this->actingAs($this->adminUser);
    $consumption = Consumption::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$consumption->id");

    $response->assertStatus(204);
    $this->assertSoftDeleted('consumptions', ['id' => $consumption->id]);
});

it('should not delete consumption if unauthorized', function () {
    $this->actingAs($this->user);
    $consumption = Consumption::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$consumption->id");

    $response->assertStatus(403);
    $this->assertDatabaseHas('consumptions', ['id' => $consumption->id]);
});

it('should not delete consumption if unauthenticated', function () {
    $consumption = Consumption::factory()->create();

    $response = $this->deleteJson("/$this->baseUrl/$consumption->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('consumptions', ['id' => $consumption->id]);
});
