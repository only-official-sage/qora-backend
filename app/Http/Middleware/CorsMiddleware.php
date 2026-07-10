<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $config = config('cors');

        $path = $request->path();
        $method = $request->getMethod();

        // Check if the current path matches any of the configured paths
        $matchesPath = false;
        foreach ($config['paths'] as $configuredPath) {
            // Convert glob pattern to regex
            $pattern = str_replace(['*', '?'], ['.*', '.'], preg_quote($configuredPath, '/'));
            $pattern = '/^' . $pattern . '$/';
            if (preg_match($pattern, $path)) {
                $matchesPath = true;
                break;
            }
        }

        if (!$matchesPath) {
            return $next($request);
        }

        // Handle preflight OPTIONS request
        if ($method === 'OPTIONS') {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        // Determine allowed origin
        $origin = $request->header('Origin');
        $allowedOrigins = $config['allowed_origins'];
        $allowedOriginPatterns = $config['allowed_origins_patterns'];

        $allowOrigin = null;
        if (in_array($origin, $allowedOrigins)) {
            $allowOrigin = $origin;
        } else {
            foreach ($allowedOriginPatterns as $pattern) {
                if (fnmatch($pattern, $origin)) {
                    $allowOrigin = $origin;
                    break;
                }
            }
        }

        // If we have a matching origin, set CORS headers
        if ($allowOrigin !== null) {
            $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
            $response->headers->set('Vary', 'Origin');
        }

        // Set allowed methods
        $allowedMethods = $config['allowed_methods'];
        if ($allowedMethods === ['*']) {
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
        } else {
            $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        }

        // Set allowed headers
        $allowedHeaders = $config['allowed_headers'];
        if ($allowedHeaders === ['*']) {
            $response->headers->set('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers') ?: 'Content-Type, Authorization, X-Requested-With');
        } else {
            $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }

        // Exposed headers
        if (!empty($config['exposed_headers'])) {
            $response->headers->set('Access-Control-Expose-Headers', implode(', ', $config['exposed_headers']));
        }

        // Max age
        if ($config['max_age'] > 0) {
            $response->headers->set('Access-Control-Max-Age', $config['max_age']);
        }

        // Credentials support
        if ($config['supports_credentials']) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
