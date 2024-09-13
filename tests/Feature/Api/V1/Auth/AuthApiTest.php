<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1';
    $this->user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);
});

it('should register a new user', function () {
    $registerData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson("{$this->baseUrl}/register", $registerData);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'token',
        'user' => [
            'id',
            'name',
            'email',
        ],
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
    ]);
});

it('should login a user', function () {
    $loginData = [
        'email' => $this->user->email,
        'password' => 'password',
    ];

    $response = $this->postJson("{$this->baseUrl}/login", $loginData);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'token',
        'user' => [
            'id',
            'name',
            'email',
        ],
    ]);
});

it('should logout a user', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson("{$this->baseUrl}/logout");

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Logged Out Successfully!',
    ]);
});

it('should logout other sessions', function () {
    Sanctum::actingAs($this->user);

    $this->user->createToken('test-token-1');
    $this->user->createToken('test-token-2');

    $response = $this->postJson("{$this->baseUrl}/logout-other-sessions");

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Logged Out Other Sessions Successfully!',
    ]);
});

it('should logout a specific session', function () {
    Sanctum::actingAs($this->user);

    $token = $this->user->createToken('test-token');
    $tokenId = $token->accessToken->id;

    $response = $this->postJson("{$this->baseUrl}/logout-session/{$tokenId}");

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Logged Out Session Successfully!',
    ]);
});

it('should return 404 for non-existing session on logout', function () {
    Sanctum::actingAs($this->user);

    $nonExistingTokenId = 999;

    $response = $this->postJson("{$this->baseUrl}/logout-session/{$nonExistingTokenId}");

    $response->assertStatus(404);
    $response->assertJson([
        'message' => 'Session Not Found!',
    ]);
});

it('should change password', function () {
    Sanctum::actingAs($this->user);

    $changePasswordData = [
        'current_password' => 'password',
        'new_password' => 'newpassword',
        'new_password_confirmation' => 'newpassword',
    ];

    $response = $this->postJson("{$this->baseUrl}/change-password", $changePasswordData);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Password Updated Successfully!',
    ]);

    $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
});

it('should get my details', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson("{$this->baseUrl}/my-details");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
        ],
    ]);
});

it('should update my details', function () {
    Sanctum::actingAs($this->user);

    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updatedemail@example.com',
        'password' => 'newpassword',
    ];

    $response = $this->putJson("{$this->baseUrl}/my-details", $updateData);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
        ],
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'Updated Name',
        'email' => 'updatedemail@example.com',
    ]);

    $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
});
