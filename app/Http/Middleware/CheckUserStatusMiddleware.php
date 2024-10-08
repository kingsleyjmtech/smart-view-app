<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatusMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->status !== User::ACTIVE_STATUS) {
                return response()->json([
                    'message' => 'Your account is inactive. Please contact support.',
                ], 403);
            }
        } else {
            return response()->json([
                'message' => 'You are unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
