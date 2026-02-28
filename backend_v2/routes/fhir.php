<?php

use App\Http\Controllers\Api\Fhir\HealthcareServiceController;
use App\Http\Controllers\Api\Fhir\LocationController;
use App\Http\Controllers\Api\Fhir\MetadataController;
use App\Http\Controllers\Api\Fhir\OrganizationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FHIR R4 API Routes
|--------------------------------------------------------------------------
|
| Routes for the FHIR R4-compliant read-only API. Exposes HFR data as
| Organization, Location, and HealthcareService resources.
|
| Base URL: /api/fhir
|
| Authentication: X-API-Key header required on all endpoints except /metadata.
|
*/

// ── CapabilityStatement (public, no auth) ──────────────────────
Route::get('metadata', [MetadataController::class, 'index'])
    ->name('fhir.metadata');

// ── Authenticated FHIR Endpoints ───────────────────────────────
Route::middleware(['verify.api.key', 'log.api.request'])->group(function () {

    // Organization (all facility types)
    Route::get('Organization', [OrganizationController::class, 'search'])
        ->name('fhir.organization.search');
    Route::get('Organization/{id}', [OrganizationController::class, 'read'])
        ->name('fhir.organization.read');

    // Location (physical sites)
    Route::get('Location', [LocationController::class, 'search'])
        ->name('fhir.location.search');
    Route::get('Location/{id}', [LocationController::class, 'read'])
        ->name('fhir.location.read');

    // HealthcareService (services offered by facilities)
    Route::get('HealthcareService', [HealthcareServiceController::class, 'search'])
        ->name('fhir.healthcareservice.search');
    Route::get('HealthcareService/{id}', [HealthcareServiceController::class, 'read'])
        ->name('fhir.healthcareservice.read');
});
