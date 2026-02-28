<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    /**
     * Verify the X-API-Key header against the api_clients table.
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');

        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key is required. Include your key in the X-API-Key header.',
                'code' => 'MISSING_API_KEY',
            ], 401);
        }

        $hash = ApiClient::hashApiKey($apiKey);
        $client = ApiClient::where('api_key_hash', $hash)->first();

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API key.',
                'code' => 'INVALID_API_KEY',
            ], 401);
        }

        if (!$client->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your API key has been deactivated. Contact support.',
                'code' => 'API_KEY_INACTIVE',
            ], 403);
        }

        if ($client->hasExpired()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your API key has expired. Please request a new one.',
                'code' => 'API_KEY_EXPIRED',
            ], 403);
        }

        // Bind the client to the request for downstream use
        $request->attributes->set('api_client', $client);
        $request->attributes->set('api_client_id', $client->id);

        // Touch last used (non-blocking, fire and forget)
        $client->touchLastUsed();

        return $next($request);
    }
}
