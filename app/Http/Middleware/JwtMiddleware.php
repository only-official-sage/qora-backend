<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;

class JwtMiddleware
{
    private JwtService $jwtService;

    public function __construct()
    {
        $this->jwtService = new JwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        // 1. Try Authorization header first (Bearer token)
        $token = $request->bearerToken();

        // 2. Fallback: Check HTTP-only 'token' cookie
        if (! $token && $request->cookies->has('token')) {
            $token = $request->cookies->get('token');
        }

        if (! $token) {
            return response()->json(['message' => 'Unauthorized - No token'], 401);
        }

        $admin = $this->jwtService->validate($token);

        if (! $admin) {
            return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
        }

        // Set the admin as the authenticated user for the request
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        return $next($request);
    }
}
