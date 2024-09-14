<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->baseUrl = 'api/v1';
    $this->user = User::factory()->create([
        'email_verified_at' => null,
    ]);
});

it('should verify email with valid link', function () {
    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $this->user->id, 'hash' => sha1($this->user->getEmailForVerification())]
    );

    $response = $this->getJson($verificationUrl);

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => 'Email verified successfully.',
    ]);

    $this->assertNotNull($this->user->fresh()->email_verified_at);

    Event::assertDispatched(Verified::class);
});

it('should not verify email with invalid user id', function () {
    $invalidUserId = 999;
    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $invalidUserId, 'hash' => sha1($this->user->getEmailForVerification())]
    );

    $response = $this->getJson($verificationUrl);

    $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    $response->assertJson([
        'message' => 'User not found.',
    ]);
});

it('should not verify email with invalid hash', function () {
    $invalidHash = 'invalid-hash';
    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $this->user->id, 'hash' => $invalidHash]
    );

    $response = $this->getJson($verificationUrl);

    $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'message' => 'This verification link is invalid.',
    ]);
});

it('should not verify email if already verified', function () {
    $this->user->markEmailAsVerified();

    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $this->user->id, 'hash' => sha1($this->user->getEmailForVerification())]
    );

    $response = $this->getJson($verificationUrl);

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => 'Email already verified.',
    ]);
});

it('should resend verification email', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson("{$this->baseUrl}/verify/email/resend");

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => 'Email verification link sent',
    ]);
});

it('should not resend verification email if already verified', function () {
    $this->user->markEmailAsVerified();
    Sanctum::actingAs($this->user);

    $response = $this->postJson("{$this->baseUrl}/verify/email/resend");

    $response->assertStatus(ResponseAlias::HTTP_OK);
    $response->assertJson([
        'message' => 'Email already verified',
    ]);
});
