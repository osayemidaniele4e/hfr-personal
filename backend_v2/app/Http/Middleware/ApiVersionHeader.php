<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersionHeader
{
    /**
     * Add API version and deprecation headers to every response.
     */
    public function handle(Request $request, Closure $next, string $version = 'v1')
    {
        $response = $next($request);

        $response->headers->set('X-API-Version', $version);
        $response->headers->set('X-RateLimit-Limit', $request->attributes->get('api_client')?->rate_limit ?? 60);
        $response->headers->set('Content-Type', 'application/json');

        // Future: add Sunset header for deprecated versions
        // $response->headers->set('Sunset', 'Sat, 01 Jan 2028 00:00:00 GMT');
        // $response->headers->set('Deprecation', 'true');

        return $response;
    }
}
