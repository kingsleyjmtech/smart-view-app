<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUserTimezoneMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->timezone) {
                config(['app.timezone' => $user->timezone]);
            }
        }

        return $next($request);
    }
}
