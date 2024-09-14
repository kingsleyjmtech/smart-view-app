<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateMyDetailsRequest;
use App\Http\Resources\Auth\MyDetailsResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthApiController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::query()->create([
                'name' => $request->name,
                'email' => $request->email,
                'timezone' => $request->timezone,
                'password' => Hash::make($request->password),
                'status' => 'Active',
            ]);

            event(new Registered($user));

            $token = $user->createToken(
                name: 'auth-token'
            )->plainTextToken;

            $response = [
                'message' => 'User Registered Successfully!',
                'token' => $token,
                'user' => new MyDetailsResource($user),
            ];

            return response($response, ResponseAlias::HTTP_OK);
        }, 5);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $token = $request->user()->createToken(
            name: 'auth-token'
        )->plainTextToken;

        /* @var User $user */
        $user = $request->user();

        $response = [
            'token' => $token,
            'user' => new MyDetailsResource($user),
        ];

        return response($response, ResponseAlias::HTTP_OK);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return [
            'message' => 'Logged Out Successfully!',
        ];
    }

    public function logoutOtherSessions()
    {
        auth()->user()->tokens()->where('id', '!=', auth()->user()->currentAccessToken()->id)->delete();

        return [
            'message' => 'Logged Out Other Sessions Successfully!',
        ];
    }

    public function logoutSession(int $id)
    {
        $success = auth()->user()->tokens()->where('id', $id)->delete();

        if (! $success) {
            return response([
                'message' => 'Session Not Found!',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return response([
            'message' => 'Logged Out Session Successfully!',
        ], ResponseAlias::HTTP_OK);
    }

    public function changePassword(ChangePasswordRequest $changePasswordRequest)
    {
        User::find(auth()->user()->id)->update(['password' => Hash::make($changePasswordRequest->new_password)]);

        return [
            'message' => 'Password Updated Successfully!',
        ];
    }

    public function myDetails()
    {
        /* @var User $user */
        $user = auth()->user();

        return new MyDetailsResource($user);
    }

    public function updateMyDetails(UpdateMyDetailsRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /* @var User $user */
            $user = auth()->user();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'timezone' => $request->timezone,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            return new MyDetailsResource($user);
        });
    }
}
