<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKeyRequestController extends Controller
{
    /**
     * Submit a public API key request.
     * No authentication required — this is the self-service form endpoint.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'organisation' => 'nullable|string|max:255',
            'use_case'     => 'required|string|min:20|max:2000',
        ], [
            'use_case.min' => 'Please provide at least 20 characters describing your use case.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'code'    => 'VALIDATION_ERROR',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check if there's already a pending or approved request for this email
        $existing = ApiClient::where('email', $request->email)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            $msg = $existing->status === 'pending'
                ? 'You already have a pending API key request. Please wait for it to be reviewed.'
                : 'An API key has already been issued for this email address. Contact the administrator if you need a new key.';

            return response()->json([
                'status'  => 'error',
                'message' => $msg,
                'code'    => 'DUPLICATE_REQUEST',
            ], 409);
        }

        // Create a pending request (no key generated yet)
        ApiClient::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'organisation' => $request->organisation,
            'api_key'      => 'pending',
            'api_key_hash' => 'pending',
            'rate_limit'   => 60,
            'is_active'    => false,
            'status'       => 'pending',
            'use_case'     => $request->use_case,
            'description'  => $request->use_case,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Your API key request has been submitted successfully. You will receive an email once it is reviewed.',
        ], 201);
    }

    /**
     * Check the status of a request by email.
     */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'A valid email address is required.',
                'code'   => 'VALIDATION_ERROR',
            ], 422);
        }

        $client = ApiClient::where('email', $request->email)
            ->latest()
            ->first();

        if (!$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No API key request found for this email address.',
                'code'    => 'NOT_FOUND',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'name'         => $client->name,
                'email'        => $client->email,
                'request_status' => $client->status,
                'submitted_at' => $client->created_at->toIso8601String(),
                'reviewed_at'  => $client->reviewed_at?->toIso8601String(),
            ],
        ]);
    }
}
