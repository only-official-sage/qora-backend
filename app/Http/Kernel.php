<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\CorsMiddleware::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Web group middleware if needed
        ],
        'api' => [
            // API group middleware if needed
        ],
    ];

    protected $routeMiddleware = [
        'jwt' => \App\Http\Middleware\JwtMiddleware::class,
    ];
}
