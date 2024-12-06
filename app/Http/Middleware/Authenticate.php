<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('api/*')) {
            return null;
        }

        return $request->expectsJson() ? null : route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        Log::error('Authentication failed', [
            'guards' => $guards,
            'headers' => $request->headers->all()
        ]);

        abort(response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401));
    }
}
