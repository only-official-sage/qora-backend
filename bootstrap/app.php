<?php

use App\Http\Middleware\ForceApiJson;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([ForceApiJson::class]);
        $middleware->alias(['jwt' => JwtMiddleware::class]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });
    })
    ->create();
