<?php

use App\Http\Controllers\Frontend\API\FrontendController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//         return $request->user();
// });

Route::group(['middleware' => 'cors'], function () {

    Route::get('slider', [FrontendController::class, 'slider']);

    Route::get('origin', [FrontendController::class, 'origin']);
    Route::get('process', [FrontendController::class, 'process']);
    Route::get('process-item', [FrontendController::class, 'processItem']);

    Route::get('facility-type', [FrontendController::class, 'facilityType']);
    Route::get('facility-level', [FrontendController::class, 'facilityLevel']);

    Route::get('states', [FrontendController::class, 'states']);

    Route::post('lgas-by-state', [FrontendController::class, 'getLgaListByStateId']);
    Route::post('ward-by-lga', [FrontendController::class, 'getWardListByLGA']);

    Route::get('ownership', [FrontendController::class, 'getOwnership']);
    Route::post('ownership-type', [FrontendController::class, 'getOwnershipType']);

    Route::get('operational-status', [FrontendController::class, 'getOperationalStatus']);
    Route::get('registration-status', [FrontendController::class, 'getRegistrationStatus']);
    Route::get('license-status', [FrontendController::class, 'getLicenseStatus']);
    Route::get('accreditation-status', [FrontendController::class, 'getAccreditationStatus']);

    
    Route::get('service-category', [FrontendController::class, 'getServiceCategory']);
    Route::post('services-by-category', [FrontendController::class, 'getServicesByCategory']);



    Route::post('facilities-hospitals-search/{page?}', [FrontendController::class, 'searchHospitals']);
    Route::get('facilities-hospital/{facilityId}', [FrontendController::class, 'HospitalDetail']);
    Route::post('facilities-hospitals-search2/{page?}', [FrontendController::class, 'searchHospitals2']);
    Route::get('facilities-hospitals-search3', [FrontendController::class, 'searchHospitals3']);
    // Route::get('facilities-hospitals-search3/{page?}', [FrontendController::class, 'searchHospitals3']);
    Route::post('facilities/pharmacies-list/{page?}', [FrontendController::class, 'searchPharmacy']);
    Route::post('facilities/lab-list/{page?}', [FrontendController::class, 'searchLab']);
    Route::post('facilities/imaging-list/{page?}', [FrontendController::class, 'searchImaging']);



    Route::post('contact', 'ContactUsController@store');

    Route::post('download-facilities', [FrontendController::class, 'saveDownloadUserRecords']);
    Route::get('resources', [FrontendController::class, 'resource_index']);
    Route::get('facilities-latest-updates', [FrontendController::class, 'getUpdates']);
    // Route::get('/get-updates', 'Frontend\API\FrontendController@getUpdates');


    Route::post('facilitiesbyLga', [FrontendController::class, 'getFacilitesByLGA']);
    Route::post('googlemap', [FrontendController::class, 'getFacilitesGMap']);
});
