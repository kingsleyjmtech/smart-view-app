<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1';
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
});

it('should send password reset link', function () {
    Password::shouldReceive('sendResetLink')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(Password::RESET_LINK_SENT);

    $response = $this->postJson("{$this->baseUrl}/forgot-password", [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => __(Password::RESET_LINK_SENT),
    ]);
});

it('should not send password reset link to invalid email', function () {
    Password::shouldReceive('sendResetLink')
        ->once()
        ->with(['email' => 'invalid@example.com'])
        ->andReturn(Password::INVALID_USER);

    $response = $this->postJson("{$this->baseUrl}/forgot-password", [
        'email' => 'invalid@example.com',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('should reset the password with valid token', function () {
    Event::fake();

    $token = Password::createToken($this->user);

    $resetData = [
        'email' => 'test@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
        'token' => $token,
    ];

    Password::shouldReceive('reset')
        ->once()
        ->with(
            \Mockery::on(function ($credentials) use ($resetData) {
                return $credentials['email'] === $resetData['email'] &&
                       $credentials['password'] === $resetData['password'] &&
                       $credentials['password_confirmation'] === $resetData['password_confirmation'] &&
                       $credentials['token'] === $resetData['token'];
            }),
            \Mockery::on(function ($callback) use ($resetData) {
                $user = User::where('email', $resetData['email'])->first();
                $callback($user);

                return true;
            })
        )
        ->andReturn(Password::PASSWORD_RESET);

    $response = $this->postJson("{$this->baseUrl}/reset-password", $resetData);

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => 'Password reset successfully',
    ]);

    $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));

    Event::assertDispatched(PasswordReset::class);
});

it('should not reset the password with invalid token', function () {
    $resetData = [
        'email' => 'test@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
        'token' => 'invalid-token',
    ];

    Password::shouldReceive('reset')
        ->once()
        ->andReturn(Password::INVALID_TOKEN);

    $response = $this->postJson("{$this->baseUrl}/reset-password", $resetData);

    $response->assertStatus(422);
    $response->assertJson([
        'message' => __(Password::INVALID_TOKEN),
    ]);
});
