<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $forgotPasswordRequest)
    {
        $status = Password::sendResetLink(
            $forgotPasswordRequest->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'message' => __($status),
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function reset(ResetPasswordRequest $resetPasswordRequest)
    {
        $status = Password::reset(
            $resetPasswordRequest->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($resetPasswordRequest) {
                $user->forceFill([
                    'password' => Hash::make($resetPasswordRequest->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message' => 'Password reset successfully',
            ]);
        }

        return response([
            'message' => __($status),
        ], 422);
    }
}
