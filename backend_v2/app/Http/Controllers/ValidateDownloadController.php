<?php

namespace App\Http\Controllers;

use App\Models\Download;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateDownloadController extends Controller
{
    public function showValidate()
    {
        return view('auth.validate-download');
    }


    public function validateDownload(Request $request)
    {
        // Log::info('Token validation initiated', [
        //     'session_id' => session()->getId(),
        //     'current_time' => now(),
        //     'request_data' => $request->token,
        // ]);

        $validated = $request->validate([
            'token' => 'required|string',
        ]);


        $download = Download::where('token', $validated['token'])->first();

        if (!$download) {
            return response()->json([
                'error' => 'Token not found.',
                'redirect1' => env('FRONTEND_URL') . '/datadownloads',
            ], 400);
        }

        if (now()->gt($download->token_expires_at)) {
            return response()->json([
                'error' => 'Token has expired.',
                'redirect1' => env('FRONTEND_URL') . '/datadownloads',
            ], 400);
        }

        // Success – return a redirect URL
        return response()->json([
            'message' => 'Verification successful!',
            // 'redirect' => env('FRONTEND_URL') . '/facilitieslist'
            'redirect' => env('FRONTEND_URL') . '/facilitieslist?verified=true',

        ]);
    }
}
