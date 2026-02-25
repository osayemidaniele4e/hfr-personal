<?php

use App\Http\Controllers\Api\V1\FacilityController;
use App\Http\Controllers\Api\V1\LookupController;
use App\Http\Controllers\Api\V1\PharmacyController;
use App\Http\Controllers\Api\V1\LaboratoryController;
use App\Http\Controllers\Api\V1\ImagingController;
use App\Http\Controllers\Api\V1\ApiKeyRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HFR External API v1 Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by RouteServiceProvider under the prefix
| /api/v1 with api_external middleware group applied (API key auth,
| per-client rate limiting, request logging, version headers).
|
| Existing /api/* routes are untouched for backward compatibility.
|
*/

// Health check (no auth required)
Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'HFR External API v1',
        'version' => 'v1',
        'documentation' => url('/developers'),
        'endpoints' => [
            'facilities' => url('/api/v1/facilities'),
            'pharmacies' => url('/api/v1/pharmacies'),
            'laboratories' => url('/api/v1/laboratories'),
            'imaging' => url('/api/v1/imaging'),
            'lookups' => url('/api/v1/lookups/states'),
            'statistics' => url('/api/v1/facilities/statistics'),
        ],
    ]);
});

// Public: Request an API key (no auth required, rate-limited by IP)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('request-key', [ApiKeyRequestController::class, 'store']);
    Route::get('request-key/status', [ApiKeyRequestController::class, 'status']);
});

// All endpoints below require a valid API key (X-API-Key header)
Route::middleware(['verify.api.key', 'log.api.request'])->group(function () {

    // ── Facilities (Hospitals & Clinics) ──────────────────────────────
    Route::get('facilities', [FacilityController::class, 'index']);
    Route::get('facilities/statistics', [FacilityController::class, 'statistics']);
    Route::get('facilities/{id}', [FacilityController::class, 'show'])->where('id', '[0-9]+');
    Route::get('facilities/{id}/services', [FacilityController::class, 'services'])->where('id', '[0-9]+');

    // ── Pharmacies ────────────────────────────────────────────────────
    Route::get('pharmacies', [PharmacyController::class, 'index']);
    Route::get('pharmacies/{id}', [PharmacyController::class, 'show'])->where('id', '[0-9]+');

    // ── Laboratories ──────────────────────────────────────────────────
    Route::get('laboratories', [LaboratoryController::class, 'index']);
    Route::get('laboratories/{id}', [LaboratoryController::class, 'show'])->where('id', '[0-9]+');

    // ── Imaging / Radiology ───────────────────────────────────────────
    Route::get('imaging', [ImagingController::class, 'index']);
    Route::get('imaging/{id}', [ImagingController::class, 'show'])->where('id', '[0-9]+');

    // ── Lookups / Reference Data ──────────────────────────────────────
    Route::prefix('lookups')->group(function () {
        Route::get('states', [LookupController::class, 'states']);
        Route::get('lgas', [LookupController::class, 'lgas']);
        Route::get('wards', [LookupController::class, 'wards']);
        Route::get('facility-types', [LookupController::class, 'facilityTypes']);
        Route::get('facility-levels', [LookupController::class, 'facilityLevels']);
        Route::get('ownership', [LookupController::class, 'ownership']);
        Route::get('ownership-types', [LookupController::class, 'ownershipTypes']);
        Route::get('operational-statuses', [LookupController::class, 'operationalStatuses']);
        Route::get('registration-statuses', [LookupController::class, 'registrationStatuses']);
        Route::get('license-statuses', [LookupController::class, 'licenseStatuses']);
        Route::get('accreditation-statuses', [LookupController::class, 'accreditationStatuses']);
        Route::get('service-categories', [LookupController::class, 'serviceCategories']);
        Route::get('services', [LookupController::class, 'services']);
    });
});
