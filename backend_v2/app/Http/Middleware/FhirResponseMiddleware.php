<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * FHIR Response Middleware
 *
 * Sets the Content-Type header to application/fhir+json on all FHIR
 * endpoint responses as required by the FHIR R4 specification.
 */
class FhirResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Validate Accept header — FHIR only supports JSON in this implementation
        $accept = $request->header('Accept', '*/*');
        if (
            $accept !== '*/*'
            && !str_contains($accept, 'application/fhir+json')
            && !str_contains($accept, 'application/json')
            && !str_contains($accept, '*/*')
        ) {
            return response()->json([
                'resourceType' => 'OperationOutcome',
                'issue' => [[
                    'severity' => 'error',
                    'code' => 'not-supported',
                    'diagnostics' => 'Only application/fhir+json is supported. '
                        . 'Set Accept header to application/fhir+json or application/json.',
                ]],
            ], 406, ['Content-Type' => 'application/fhir+json; fhirVersion=4.0']);
        }

        $response = $next($request);

        // Ensure FHIR content type on all responses
        $response->headers->set('Content-Type', 'application/fhir+json; fhirVersion=4.0');

        return $response;
    }
}
