<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    /**
     * Log every external API request for analytics and auditing.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000); // ms

        try {
            ApiRequestLog::create([
                'api_client_id' => $request->attributes->get('api_client_id'),
                'method' => $request->method(),
                'endpoint' => '/' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => $duration,
                'query_params' => $request->query() ?: null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail — logging should never break the API
            Log::error('API request log failed: ' . $e->getMessage());
        }

        return $response;
    }
}
