<?php

namespace App\Http;

use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        CorsMiddleware::class,
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
        'jwt' => JwtMiddleware::class,
    ];
}
