<?php

namespace App\Http\Controllers\Frontend\API;

use App\Models\Download;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DownloadVerificationCode;
use App\Models\Website\API\Origin;
use App\Models\Website\API\Process;
use App\Models\Website\API\ProcessItem;
use App\Models\Website\API\Slider;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Notification;

// use Notification;
use App\Notifications\SendDownloadVerificationCode;
use App\Models\Resource;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


/**
 * @group Public API (Frontend)
 *
 * Public-facing API endpoints for the website.
 *
 * These endpoints power the frontend app and provide public data such as
 * sliders, process information, facility metadata, states, LGAs, wards,
 * ownership categories, and other lookup lists.
 */
class FrontendController extends Controller
{

    /**
     * Get homepage slider content.
     *
     * Returns all slider images/text used on the frontend homepage banner.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Welcome",
     *       "status": "",
     *       "image_url": "banner1.jpg"
     *     }
     *   ]
     * }
     */
    public function slider(): JsonResponse
    {
        $sliders = Slider::all();
        return response()->json([
            'success' => true,
            'data' => $sliders
        ], 200);
    }


    /**
     * Get process items.
     *
     * Returns the list of process steps displayed on the frontend process section.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Step 1"
     *     }
     *   ]
     * }
     */
    public function processItem(): JsonResponse
    {
        $processItems = ProcessItem::all();
        return response()->json([
            'success' => true,
            'data' => $processItems
        ], 200);
    }



     /**
     * Get the origin section content.
     *
     * Returns the textual/visual content displayed under the “Origin” section
     * of the website frontend.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "title": "Where it all started",
     *     "content": "..."
     *   }
     * }
     */
    public function origin(): JsonResponse
    {
        $origin = Origin::where('id', 1)->first();
        return response()->json([
            'success' => true,
            'data' => $origin
        ], 200);
    }


    /**
     * Get the process section content.
     *
     * Returns the main "Our Process" text or visual content shown on the
     * frontend website.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *      "title": "...",
     *     "content": "Process description..."
     *   }
     * }
     */
    public function process(): JsonResponse
    {
        $process = Process::where('id', 1)->first();
        return response()->json([
            'success' => true,
            'data' => $process
        ], 200);
    }


    /**
     * Get list of facility types.
     *
     * Returns all available facility types (e.g. Hospital, Clinic, Pharmacy).
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Hospital", "created_at":"...","updated_at":"..." }
     *   ]
     * }
     */
    public function facilityType(): JsonResponse
    {
        $results = Cache::remember('hfr_facility_types', 3600, function () {
            return DB::table('lst_facility_types')
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


    /**
     * Get list of facility levels.
     *
     * Returns the different levels of care (e.g. Primary, Secondary).
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *    { "id": 1, "name": "Primary" }
     *   ]
     * }
     */
    public function facilityLevel(): JsonResponse
    {
        $results = DB::table('lst_level_of_care')
            ->select('id', 'name')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


     /**
     * Get list of states.
     *
     * Returns all states available in the system.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Lagos"}
     *   ]
     * }
     */
    public function states(): JsonResponse
    {
        $results = Cache::remember('hfr_states', 3600, function () {
            return DB::table('ou_states')
                ->select('id', 'name')
                ->orderByRaw('name ASC')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


      /**
     * Get LGAs by State ID.
     *
     * Returns all Local Government Areas (LGAs) for a given state.
     *
     * @bodyParam state_id integer required The ID of the state.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 101, "name": "Ikeja" }
     *   ]
     * }
     */
    public function getLgaListByStateId(Request $request): JsonResponse
    {
        // \Log::info($request);
        $results = DB::table('ou_lgas')
            ->select('name', 'id')
            ->where('state_id', $request->state_id)
            ->orderByRaw('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


     /**
     * Get wards by LGA ID.
     *
     * Returns all wards under the specified LGA.
     *
     * @bodyParam lga_id integer required The ID of the LGA.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 5001, "name": "Ward A" }
     *   ]
     * }
     */
    public function getWardListByLGA(Request $request): JsonResponse
    {
        // \Log::info($request);
        $results = DB::table('ou_wards')
            ->select('name', 'id')
            ->where('lga_id', $request->lga_id)
            ->orderByRaw('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }



    /**
     * Get facility ownership categories.
     *
     * Returns all ownership categories such as Public, Private, Mission, etc.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Public" }
     *   ]
     * }
     */
    public function getOwnership(Request $request): JsonResponse
    {
        $results = DB::table('lst_ownerships')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


     /**
     * Get ownership types by ownership category.
     *
     * Returns all ownership types based on the selected ownership category.
     *
     * @bodyParam ownership_id integer required The ID of the ownership category.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 3, "type": "Private For-Profit" }
     *   ]
     * }
     */
    public function getOwnershipType(Request $request): JsonResponse
    {
        $results =  DB::table('lst_ownership_types')
            ->select('id', 'type')
            ->where('ownership_id', $request->ownership_id)
            ->orderByRaw('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }



        /**
     * Get operational statuses.
     *
     * Returns the list of operational statuses for facilities.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "status": "Operational" }
     *   ]
     * }
     */

    public function getOperationalStatus(Request $request): JsonResponse
    {
        $results =  DB::table('lst_oparational_status')
            ->select('id', 'status')
            ->where('category', '1')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


        /**
     * Get registration statuses.
     *
     * Returns the possible registration statuses of facilities.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "status": "Registered" }
     *   ]
     * }
     */

    public function getRegistrationStatus(Request $request): JsonResponse
    {
        $results =  DB::table('lst_registration_status')
            ->select('id', 'status')
            ->where('category', '1')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


    /**
 * Get all license statuses
 *
 * Returns all items from lst_license_status
 *
 * @response 200 {
 *   "success": true,
 *   "data": [
 *      {"id": 1, "status": "Active"},
 *      {"id": 2, "status": "Expired"}
 *   ]
 * }
 */
    public function getLicenseStatus(Request $request): JsonResponse
    {
        $results = DB::table('lst_license_status')
            ->select('id', 'status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


    /**
 * Get all hospital service categories
 *
 * Returns all items from lst_hosp_service_category
 *
 * @response 200 {
 *   "success": true,
 *   "data": [
 *      {"id": 1, "name": "Outpatient"},
 *      {"id": 2, "name": "Inpatient"}
 *   ]
 * }
 */
    public function getServiceCategory(Request $request): JsonResponse
    {
        $results = DB::table('lst_hosp_service_category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


    /**
 * Get services by category
 *
 * @queryParam service_category_id int required The ID of the service category.
 *
 * @response 200 {
 *   "success": true,
 *   "data": [
 *      {"id": 1, "name": "Cardiology"},
 *      {"id": 2, "name": "Radiology"}
 *   ]
 * }
 */
    public function getServicesByCategory(Request $request): JsonResponse
    {
        $results =  DB::table('lst_hosp_services')
            ->select('id', 'name')
            ->where('service_category_id', $request->service_category_id)
            ->orderByRaw('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


/**
 * Get all accreditation statuses
 *
 *
 * @response 200 {
 *   "success": true,
 *   "data": [
 *      {"id": 1, "status": "Accredited"},
 *      {"id": 2, "status": "Pending"}
 *   ]
 * }
 */
    function getAccreditationStatus()
    {
        $results = DB::table('lst_accreditation_status')
            ->select('id', 'status')
            ->get();


        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }


/**
 * Search hospitals with filters
 *
 * @bodyParam state_id int optional Filter by state ID.
 * @bodyParam lga_id int optional Filter by LGA ID.
 * @bodyParam ward_id int optional Filter by ward ID.
 * @bodyParam facility_name string optional Search by facility name.
 * @bodyParam geo_codes int optional Geo code filter.
 * @bodyParam facility_level_id int optional Facility level filter.
 * @bodyParam ownership_id int optional Ownership filter.
 * @bodyParam ownership_type_id int optional Ownership type filter.
 * @bodyParam operational_status_id int optional Operational status filter.
 * @bodyParam registration_status_id int optional Registration status filter.
 * @bodyParam license_status_id int optional License status filter.
 * @bodyParam service_type int optional 1=Outpatient, 2=Inpatient.
 * @bodyParam services array optional List of service IDs.
 *
 * @response 200 {
 *   "success": true,
 *   "data": { ...paginated hospital results... }
 * }
 */
//----------------------------------------------------------
    // public function searchHospitals(Request $request)
    // {
    //     \Log::info($request);

    //     // Extracting request parameters
    //     $ward_id = $request->ward_id == 0 ? '' : $request->ward_id;
    //     $facility_level_id = $request->facility_level_id == 0 ? '' : $request->facility_level_id;
    //     $ownership_id = $request->ownership_id == 0 ? '' : $request->ownership_id;
    //     $ownership_type_id = $request->ownership_type_id == 0 ? '' : $request->ownership_type_id;
    //     $operational_status_id = $request->operational_status_id == 0 ? '' : $request->operational_status_id;
    //     $registration_status_id = $request->registration_status_id == 0 ? '' : $request->registration_status_id;
    //     $license_status_id = $request->license_status_id == 0 ? '' : $request->license_status_id;
    //     // \Log::info($request);
    //     // Handling geo_codes conditions (compatible with PHP 7)
    //     if ($request->geo_codes == 0) {
    //         $cond = "<>";
    //         $value = 'XXX';
    //     } elseif ($request->geo_codes == 1) {
    //         $cond = "<>";
    //         $value = '';
    //     } elseif ($request->geo_codes == 2) {
    //         $cond = "=";
    //         $value = '';
    //     } else {
    //         $cond = "<>";
    //         $value = '';
    //     }

    //     // Handling service type conditions
    //     $outpatient = $request->service_type == 1 ? 'Yes' : '';
    //     $inpatient = $request->service_type == 2 ? 'Yes' : '';

    //     // Safely handle `services` input (single or multiple)
    //     $serviceIds = [];

    //     if (!empty($request->services)) {
    //         $serviceIds = is_array($request->services)
    //             ? $request->services
    //             : explode(',', $request->services);
    //     }


    //     // Get hospital IDs that offer those services
    //     if (!empty($serviceIds)) {
    //         $hospital = DB::select("
    //         SELECT DISTINCT hospital_id
    //         FROM hs_hospital_services
    //         WHERE service_id IN (" . implode(",", $serviceIds) . ")
    //     ");

    //         $hospital_with_services = array_column($hospital, 'hospital_id');
    //     } else {
    //         $hospital = DB::select("SELECT id FROM hospital_details");
    //         $hospital_with_services = array_column($hospital, 'id');
    //     }

    //     // Get 'per_page' from the request, defaulting to 50 if not provided
    //     $perPage = $request->input('per_page', 25); // Laravel 5 way to set default value for pagination

    //     $data['facilities'] = DB::table('hs_hospitals_history')
    //         ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
    //         ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
    //         ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
    //         ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
    //         ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
    //         ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')
    //         ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
    //         ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
    //         ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')

    //         ->select(
    //             'hs_hospitals_history.*',
    //             'ou_states.name as state_name',
    //             'ou_lgas.name as lga_name',
    //             'ou_wards.name as ward_name',
    //             'lst_level_of_care.name as facility_level_name',
    //             'lst_ownerships.name as ownership_name',
    //             'lst_ownership_types.type as ownership_type',
    //             'lst_oparational_status.status as operational_status_name',
    //             'lst_registration_status.status as registration_status_name',
    //             'lst_license_status.status as license_status_name',

    //         )

    //         // ->where('hs_hospitals_history.state_id', '=', $request->state_id)
    //         // ->where('hs_hospitals_history.lga_id', '=', $request->lga_id)
    //         // ->where(DB::raw("IFNULL(hs_hospitals_history.ward_id, '')"), '=', $ward_id)

    //         ->where('hs_hospitals_history.state_id', 'like', '%' . $request->state_id . '%')
    //         ->where('hs_hospitals_history.lga_id', 'like', '%' . $request->lga_id . '%')
    //         ->where(DB::raw("IFNULL(hs_hospitals_history.ward_id, '')"), 'like', '%' . $request->ward_id . '%')

    //         ->when($ownership_type_id, function ($query) use ($ownership_type_id) {
    //             $query->where('hs_hospitals_history.ownership_type_id', 'like', '%' . $ownership_type_id . '%');
    //         })

    //         // ->when($request->search, function ($q, $search) {
    //         //     $search = strtolower(trim($search));
    //         //     return $q->where(function ($subQuery) use ($search) {
    //         //         $subQuery->whereRaw('LOWER(ou_states.name) LIKE ?', ["%$search%"])
    //         //             ->orWhereRaw('LOWER(ou_lgas.name) LIKE ?', ["%$search%"])
    //         //             ->orWhereRaw('LOWER(ou_wards.name) LIKE ?', ["%$search%"]);
    //         //     });
    //         // })

    //         ->when($request->search, function ($q, $search) {
    //             $search = strtolower(trim($search));
    //             return $q->whereRaw('LOWER(hs_hospitals_history.facility_name) LIKE ?', ["%$search%"]);
    //         })

    //         ->where('hs_hospitals_history.facility_level_id', 'like', '%' . $facility_level_id . '%')
    //         ->where('hs_hospitals_history.ownership_id', 'like', '%' . $ownership_id . '%')
    //         // ->where('hs_hospitals_history.ownership_type_id', 'like', '%' . $ownership_type_id . '%')
    //         ->where('hs_hospitals_history.operational_status_id', 'like', '%' . $operational_status_id . '%')
    //         ->where('hs_hospitals_history.registration_status_id', 'like', '%' . $registration_status_id . '%')
    //         ->where('hs_hospitals_history.license_status_id', 'like', '%' . $license_status_id . '%')
    //         // ->where(DB::raw("IFNULL(hs_hospitals_history.outpatient, '')"), 'like', '%' . $outpatient . '%')
    //         // ->where(DB::raw("IFNULL(hs_hospitals_history.inpatient, '')"), 'like', '%' . $inpatient . '%')
    //         // ->where('hs_hospitals_history.facility_name', 'like', '%' . $request->facility_name . '%')

    //         ->where(DB::raw("IFNULL(hs_hospitals_history.latitude, '')"), $cond, $value)

    //         // ->whereIn('hs_hospitals_history.id', $hospital_with_services)
    //         // ->when(!empty($request->state_id), fn($q) => $q->where('hs_hospitals_history.state_id', $request->state_id))
    //         ->whereIn('hs_hospitals_history.id', $hospital_with_services)

    //         // Only show published facilities (0 = legacy, 6 = Created, 13 = Updated) + NULL
    //         ->where(function ($q) {
    //             $q->whereIn('hs_hospitals_history.status_id', [0, 6, 13])
    //               ->orWhereNull('hs_hospitals_history.status_id');
    //         })

    //         ->orderBy('hs_hospitals_history.state_id')
    //         ->orderBy('hs_hospitals_history.lga_id')
    //         ->orderBy('hs_hospitals_history.ward_id')
    //         ->orderBy('hs_hospitals_history.facility_name')
    //         // ->orderBy('hs_hospitals_history.created_at', 'desc')
    //         ->when($request->facility_name, function ($q, $facilityName) {
    //             return $q->where(function ($subQuery) use ($facilityName) {
    //                 $subQuery->where('ou_states.name', 'like', '%' . $facilityName . '%')
    //                     ->orWhere('ou_lgas.name', 'like', '%' . $facilityName . '%')
    //                     ->orWhere('ou_wards.name', 'like', '%' . $facilityName . '%');
    //                 // ->orWhere('hs_hospitals_history.facility_name', 'like', '%' . $facilityName . '%');
    //             });
    //         })


    //         ->paginate($perPage)
    //         ->appends($request->all());

    //     // \Log::info($data['facilities']);
    //     // Returning request values
    //     $data += [
    //         'state_id' => $request->state_id,
    //         'lga_id' => $request->lga_id,
    //         'ward_id' => $request->ward_id,
    //         'facility_name' => $request->facility_name,
    //         'geo_codes' => $request->geo_codes,
    //         'facility_level_id' => $request->facility_level_id,
    //         'ownership_id' => $request->ownership_id,
    //         'ownership_type_id' => $request->ownership_type_id,
    //         'operational_status_id' => $request->operational_status_id,
    //         'registration_status_id' => $request->registration_status_id,
    //         'license_status_id' => $request->license_status_id,
    //         'service_type' => $request->service_type,
    //         'service_category_id' => $request->service_category_id,
    //         'searched' => 1
    //     ];

    //     // Returning JSON response
    //     return response()->json([
    //         'success' => true,
    //         'data' => $data
    //     ], 200);
    // }
//----------------------------------------------------------

    public function searchHospitals(Request $request)

{

    // 1. Service Filtering Logic - ONLY fetch IDs if services are selected

    $hospitalIdsFromServices = null;

    if ($request->filled('services')) {

        $serviceIds = is_array($request->services) ? $request->services : explode(',', $request->services);



        $hospitalIdsFromServices = DB::table('hs_hospital_services')

            ->whereIn('service_id', $serviceIds)

            ->distinct()

            ->pluck('hospital_id')

            ->toArray();



        // If user searched for services but none were found, return empty results immediately

        if (empty($hospitalIdsFromServices)) {

            return response()->json(['success' => true, 'data' => ['facilities' => []]], 200);

        }

    }



    // 2. Build Query

    $query = DB::table('hs_hospitals_history')

        ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')

        ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')

        ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')

        ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

        ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')

        ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')

        ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')

        ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')

        ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')

        ->select(

            'hs_hospitals_history.*',

            'ou_states.name as state_name',

            'ou_lgas.name as lga_name',

            'ou_wards.name as ward_name',

            'lst_level_of_care.name as facility_level_name',

            'lst_ownerships.name as ownership_name',

            'lst_ownership_types.type as ownership_type',

            'lst_oparational_status.status as operational_status_name',

            'lst_registration_status.status as registration_status_name',

            'lst_license_status.status as license_status_name'

        );



    // 3. Apply Filters using when() - This prevents NULL values from being excluded

    $query->when($request->state_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.state_id', $request->state_id);

    });



    $query->when($request->lga_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.lga_id', $request->lga_id);

    });



    $query->when($request->ward_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.ward_id', $request->ward_id);

    });



    $query->when($request->facility_level_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.facility_level_id', $request->facility_level_id);

    });



    $query->when($request->ownership_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.ownership_id', $request->ownership_id);

    });



    $query->when($request->operational_status_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.operational_status_id', $request->operational_status_id);

    });



    $query->when($request->registration_status_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.registration_status_id', $request->registration_status_id);

    });



    $query->when($request->license_status_id, function ($q) use ($request) {

        return $q->where('hs_hospitals_history.license_status_id', $request->license_status_id);

    });



    // 4. Text Search

    $query->when($request->search, function ($q, $search) {

        return $q->where('hs_hospitals_history.facility_name', 'LIKE', '%' . trim($search) . '%');

    });



    // 5. Geo Codes Logic - ONLY apply if specifically selected

    if ($request->filled('geo_codes')) {

        if ($request->geo_codes == 1) { // Has coordinates

            $query->whereNotNull('hs_hospitals_history.latitude')->where('hs_hospitals_history.latitude', '<>', '');

        } elseif ($request->geo_codes == 2) { // Missing coordinates

            $query->where(function($sub) {

                $sub->whereNull('hs_hospitals_history.latitude')->orWhere('hs_hospitals_history.latitude', '');

            });

        }

    }



    // 6. Service Filter - Only apply if $hospitalIdsFromServices is NOT null

    if ($hospitalIdsFromServices !== null) {

        $query->whereIn('hs_hospitals_history.id', $hospitalIdsFromServices);

    }



    // 7. Status Filter (Ensure we include everything in the CSV)

    $query->where(function ($q) {

        $q->whereIn('hs_hospitals_history.status_id', [0, 6, 13])

          ->orWhereNull('hs_hospitals_history.status_id');

    });



    // 8. Finalize

    $perPage = $request->input('per_page', 25);

    $facilities = $query->orderBy('ou_lgas.name', 'ASC') // First sort by LGA
                        ->orderBy('hs_hospitals_history.facility_name', 'ASC') // Then by Facility Name
                        ->paginate($perPage)
                        ->appends($request->all());



    return response()->json([

        'success' => true,

        'data' => array_merge($request->all(), ['facilities' => $facilities, 'searched' => 1])

    ], 200);

}

/**
 * Get facilities by LGA with summary data
 *
 * @bodyParam state_code string required Short code of the state.
 *
 * @response 200 {
 *   "success": true,
 *   "data": {
 *      "state": "Lagos",
 *      "facilities": [...],
 *      "by_ownership": [...],
 *      "by_level": [...],
 *      "geo_codes": [...]
 *   }
 * }
 */
    public function getFacilitesByLGA(Request $request)
    {
        $total_facilities_lga = DB::select("SELECT l.map_code LGA_UID,count(h.id) value
                    FROM hs_hospitals_history h
                    JOIN ou_lgas l ON l.id = h.lga_id
                    JOIN ou_states s ON s.id=l.state_id
                    WHERE s.short_code ='" . $request->state_code .
            "'GROUP BY l.map_code");


        $state = DB::table('ou_states')
            ->select('name', 'id')
            ->where('short_code', $request->state_code)
            ->get();

        $state_id = $state[0]->id;

        //get by level of care
        $by_level = DB::select("SELECT facility_level as name,COUNT(id) AS y FROM hospital_details
                WHERE state_id=" . $state_id . " GROUP BY facility_level order by facility_level");

        //by ownership
        $by_ownership =  DB::select("SELECT ownership as name,COUNT(id) AS y FROM hospital_details
                WHERE state_id=" . $state_id . "  GROUP BY ownership order by ownership");

        //fac with Geo codes
        $geo_codes =  DB::select("SELECT lga as name, cast(SUM(case when latitude <> '' then 1 else 0 end)/count(id)*100 as unsigned) as y
        FROM hospital_details WHERE state_id=" . $state_id . "  GROUP BY lga order by y desc");


        $result  = array();
        $result['state'] =  $state[0]->name;
        $result['facilities'] =  $total_facilities_lga;
        $result['by_ownership'] =  $by_ownership;
        $result['by_level'] =  $by_level;
        $result['geo_codes'] =  $geo_codes;


        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }


/**
 * Get facilities within an LGA for Google Maps
 *
 * @bodyParam lga_code string required Map code of the LGA.
 *
 * @response 200 {
 *   "success": true,
 *   "data": {
 *      "lga_name": "Ikeja",
 *      "facilities_list": [...]
 *   }
 * }
 */
    public function getFacilitesGMap(Request $request)
    {
        //get lga id and name
        $lga = DB::table('ou_lgas')
            ->select('id', 'name')
            ->where('map_code', $request->lga_code)
            ->get();

        $lga_details = array();

        foreach ($lga as $l) {
            $lga_details[0] = $l->id; //lga id
            $lga_details[1] = $l->name; //lga name
        };


        $facilities = DB::select("SELECT * FROM hospital_details where latitude != '' and
                        lga_id='" . $lga_details[0] . "'");

        $lga_name =  $lga_details[1];

        $result  = array();
        $result['lga_name'] = $lga_name;
        $result['facilities_list'] =  $facilities;

        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }



/**
 * Search pharmacies with filters
 *
 * @bodyParam state_id int optional Filter by state ID.
 * @bodyParam lga_id int optional Filter by LGA ID.
 * @bodyParam ward_id int optional Filter by ward ID.
 * @bodyParam facility_name string optional Search by facility name.
 * @bodyParam geo_codes int optional Geo code filter.
 * @bodyParam ownership_id int optional Ownership filter.
 * @bodyParam operational_status_id int optional Operational status filter.
 * @bodyParam registration_status_id int optional Registration status filter.
 * @bodyParam license_status_id int optional License status filter.
 *
 * @response 200 {
 *   "success": true,
 *   "data": { ...paginated pharmacy results... }
 * }
 */
    public function searchPharmacy(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }


        $data['facilities'] = DB::table('pharmacies')
            ->join('ou_states', 'pharmacies.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'pharmacies.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'pharmacies.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'pharmacies.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_oparational_status', 'pharmacies.operational_status_id', '=', 'lst_oparational_status.id')
            ->join('lst_registration_status', 'pharmacies.registration_status_id', '=', 'lst_registration_status.id')
            ->join('lst_license_status', 'pharmacies.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'pharmacies.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status'
            )
            ->where('pharmacies.state_id', 'like', '%' . $state_id . '%')
            ->where('pharmacies.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::raw("IFNULL(pharmacies.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('pharmacies.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('pharmacies.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('pharmacies.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('pharmacies.license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('pharmacies.facility_name', 'like', '%' . $facility_name . '%')

            // Search by Location (State, LGA, or Ward)
            // ->orWhere('ou_states.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_lgas.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_wards.name', 'like', '%' . $request->facility_name . '%')

            ->where('pharmacies.latitude', $cond, $value)
            ->orderBy('pharmacies.state_id')
            ->orderBy('pharmacies.lga_id')
            ->orderBy('pharmacies.facility_name')
            ->paginate(15)
            ->appends($request->all());



        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['searched'] = 1;



        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }


/**
 * Search laboratories with filters
 *
 * @bodyParam state_id int optional Filter by state ID.
 * @bodyParam lga_id int optional Filter by LGA ID.
 * @bodyParam ward_id int optional Filter by ward ID.
 * @bodyParam facility_name string optional Search by facility name.
 * @bodyParam facility_level_id int optional Filter by facility level.
 * @bodyParam ownership_id int optional Ownership filter.
 * @bodyParam operational_status_id int optional Operational status filter.
 * @bodyParam registration_status_id int optional Registration status filter.
 * @bodyParam license_status_id int optional License status filter.
 * @bodyParam accreditation_status_id int optional Accreditation status filter.
 * @bodyParam geo_codes int optional Geo code filter.
 *
 * @response 200 {
 *   "success": true,
 *   "data": { ...paginated lab results... }
 * }
 */
    public function searchLab(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;
        $accreditation_status_id = $request->accreditation_status_id;


        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($facility_level_id == 0) {
            $facility_level_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }
        if ($accreditation_status_id == 0) {
            $accreditation_status_id = '';
        }


        $data['facilities'] = DB::table('lb_laboratories')
            ->join('ou_states', 'lb_laboratories.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'lb_laboratories.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'lb_laboratories.ward_id', '=', 'ou_wards.id')
            ->join('lst_level_of_care', 'lb_laboratories.facility_level_id', '=', 'lst_level_of_care.id')
            ->join('lst_ownerships', 'lb_laboratories.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_oparational_status', 'lb_laboratories.operational_status_id', '=', 'lst_oparational_status.id')
            ->join('lst_registration_status', 'lb_laboratories.registration_status_id', '=', 'lst_registration_status.id')
            ->join('lst_license_status', 'lb_laboratories.license_status_id', '=', 'lst_license_status.id')
            ->join('lst_accreditation_status', 'lb_laboratories.accreditation_status_id', '=', 'lst_accreditation_status.id')
            ->select(
                'lb_laboratories.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name',
                'lst_accreditation_status.status as accreditation_status_name'
            )
            ->where('lb_laboratories.state_id', 'like', '%' . $state_id . '%')
            ->where('lb_laboratories.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::raw("IFNULL(lb_laboratories.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('lb_laboratories.facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('lb_laboratories.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('lb_laboratories.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('lb_laboratories.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('lb_laboratories.license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('lb_laboratories.accreditation_status_id', 'like', '%' . $accreditation_status_id . '%')
            ->where('lb_laboratories.facility_name', 'like', '%' . $facility_name . '%')

            // Search by Location (State, LGA, or Ward)
            // ->orWhere('ou_states.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_lgas.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_wards.name', 'like', '%' . $request->facility_name . '%')

            ->where('lb_laboratories.latitude', $cond, $value)
            ->orderBy('lb_laboratories.state_id')
            ->orderBy('lb_laboratories.lga_id')
            ->orderBy('lb_laboratories.facility_name')
            ->paginate(20)
            ->appends($request->all());


        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['facility_level_id'] = $request->facility_level_id;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['accreditation_status_id'] = $request->accreditation_status_id;
        $data['searched'] = 1;

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }


/**
 * Search Imaging Facilities
 *
 *
 * @bodyParam state_id int The ID of the state. Example: 1
 * @bodyParam lga_id int The ID of the local government area. Example: 5
 * @bodyParam ward_id int The ID of the ward. Example: 2
 * @bodyParam facility_name string Facility name to search. Example: "General Hospital"
 * @bodyParam ownership_id int Ownership type ID. Example: 1
 * @bodyParam operational_status_id int Operational status ID. Example: 2
 * @bodyParam registration_status_id int Registration status ID. Example: 1
 * @bodyParam license_status_id int License status ID. Example: 1
 * @bodyParam geo_codes int Geo code filter: 0, 1, 2. Example: 0
 *
 * @response 200 {
 *   "success": true,
 *   "data": {
 *      "facilities": [...],
 *      "state_id": 1,
 *      "lga_id": 5,
 *      "ward_id": 2,
 *      "facility_name": "General Hospital",
 *      "geo_codes": 0,
 *      "ownership_id": 1,
 *      "operational_status_id": 2,
 *      "registration_status_id": 1,
 *      "license_status_id": 1,
 *      "searched": 1
 *   }
 * }
 */
    public function searchImaging(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }

        $data['facilities'] = DB::table('im_imagings')
            ->join('ou_states', 'im_imagings.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'im_imagings.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'im_imagings.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'im_imagings.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_oparational_status', 'im_imagings.operational_status_id', '=', 'lst_oparational_status.id')
            ->join('lst_registration_status', 'im_imagings.registration_status_id', '=', 'lst_registration_status.id')
            ->join('lst_license_status', 'im_imagings.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'im_imagings.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status'
            )
            ->where('im_imagings.state_id', 'like', '%' . $state_id . '%')
            ->where('im_imagings.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::raw("IFNULL(im_imagings.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('im_imagings.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('im_imagings.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('im_imagings.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('im_imagings.license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('im_imagings.facility_name', 'like', '%' . $facility_name . '%')
            ->where('im_imagings.latitude', $cond, $value)
            ->orderBy('im_imagings.state_id')
            ->orderBy('im_imagings.lga_id')
            ->orderBy('im_imagings.facility_name')
            ->paginate(15)
            ->appends($request->all());



        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['searched'] = 1;

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }


/**
 * Save Download User Records
 *
 *
 * @bodyParam firstname string required User's first name. Example: "John"
 * @bodyParam lastname string required User's last name. Example: "Doe"
 * @bodyParam organisation string User's organisation. Example: "Health Org"
 * @bodyParam country string required User's country. Example: "Nigeria"
 * @bodyParam designation string required User's designation. Example: "Researcher"
 * @bodyParam purpose string required Purpose of download. Example: "Research"
 * @bodyParam email string required User's email. Example: "john@example.com"
 * @bodyParam captchaToken string required Google reCAPTCHA token
 *
 * @response 201 {
 *   "success": true,
 *   "message": "Download request saved. Verification email sent.",
 *   "data": { ... }
 * }
 */
    public function saveDownloadUserRecords(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'organisation' => 'nullable|string|max:100',
            'country' => 'required|string',
            'designation' => 'required|string',
            'purpose' => 'required|string|max:200',
            'email' => 'required|string|email|max:100',
            'captchaToken' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }


        // Check if the reCAPTCHA token is present
        if (!$request->input('captchaToken')) {
            return response()->json([
                'message' => 'Please complete the reCAPTCHA to proceed.'
            ], 422);
        }

        // Initialize Guzzle client
        $client = new Client();
        // \Log::info('Before reCAPTCHA verification');
        // Verify the reCAPTCHA token with Google
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'   => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('captchaToken'), // Use 'captchaToken' here
            ]
        ]);

        // Decode the response body
        $body = json_decode((string) $response->getBody(), true);

        // \Log::info('reCAPTCHA Response', ['response' => $body]);

        // Check if the reCAPTCHA verification was successful and score is above the threshold
        if (!isset($body['success']) || !$body['success']) {
            return response()->json(['message' => 'reCAPTCHA verification failed.'], 422);
        }

        \Log::info($request);
        try {
            // Store data in the database
            $download = Download::create($request->all());

            // Generate verification code
            $code = $this->generateToken();

            $download->token = $code;
            $download->token_expires_at = now()->addMinutes(15);

            $download->save();

            // Store token in session (if needed)
            session()->put('download_verify', [
                'token' => $code,
                'expire_at' => now()->addMinutes(15),
            ]);

            \Log::info('Session data:', session()->get('download_verify'));

            // $tokenUrl = route('showValidate');
            $tokenUrl = url('/validate-token') . '?token=' . $code;

            // Send verification email
            if (!$this->sendToken2Email($request->email, $code, $tokenUrl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification email',
                ], 500);
            }

            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Download request saved. Verification email sent.',
                'data' => $download,
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function sendToken2Email($email, $code, $tokenUrl)
    {

        try {
            Mail::to($email)->send(new DownloadVerificationCode($code, $tokenUrl));
            return true;
        } catch (\Exception $ex) {
            // \Log::error("Email sending failed: " . $ex->getMessage());
            return false;
        }
    }

    public function generateToken()
    {
        $code = mt_rand(12345678, 98765432);
        return $code;
    }



    /**
 * Resource Index
 *
 *
 * @response 201 {
 *   "success": true,
 *   "message": "Download request saved. Verification email sent.",
 *   "data": [...]
 * }
 */
    public function resource_index()
    {
        $resources = Resource::all();

        return response()->json([
            'success' => true,
            'message' => 'Download request saved. Verification email sent.',
            'data' => $resources,
        ], 201);
    }


    public function getUpdates444(Request $request)
    {

        //New Facilities Created This Month
        if ($request->report == 1) {
            $data['facilities'] = DB::table('hospital_details')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created this Month";
        }

        //New Facilities Created Last Month
        if ($request->report == 2) {
            $data['facilities']  = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created in the last Month";
        }

        //New Facilities Created Last 3 Months
        if ($request->report == 3) {
            $data['facilities']  = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created in the last 3 Month";
        }

        //Facilities Updated This Month
        if ($request->report == 4) {
            $data['facilities']  = DB::table('hospital_details')
                ->where('updated_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();

            $data['report'] = $data['facilities']->count() . " Facilities were updated this Month";
        }

        //Facilities Updated Last Month
        if ($request->report == 5) {
            $data['facilities']  = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were updated in the last Month";
        }

        //Facilities Updated Last 3 Month
        if ($request->report == 6) {
            $data['facilities']  = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were updated in the last 3 Month";
        }

        return response()->json([
            'success' => true,
            'message' => 'Download request saved. Verification email sent.',
            'data' => $data,
        ], 201);
    }



/**
 * Get Facility Updates (Improved)
 *
 *
 * @bodyParam report int Report type. Example: 1
 *
 * @response 200 {
 *   "success": true,
 *   "message": "Report generated successfully.",
 *   "data": {
 *       "facilities": [...],
 *       "report": "5 Facilities were created this Month"
 *   }
 * }
 */
    public function getUpdates(Request $request)
    {
        $data = []; // Initialize $data to prevent "Undefined variable" error

        // Log::info('Fetching updates', ['request' => $request->all()]);
        Log::info(Carbon::now());

        // New Facilities Created This Month
        if ($request->report == 1) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->where('hs_hospitals_history.created_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created this Month";
        }

        // New Facilities Created Last Month
        if ($request->report == 2) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->whereBetween('hs_hospitals_history.created_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created in the last Month";
        }

        // New Facilities Created Last 3 Months
        if ($request->report == 3) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->whereBetween('hs_hospitals_history.created_at', [Carbon::now()->subMonths(3), Carbon::now()])
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were created in the last 3 Months";
        }

        // Facilities Updated This Month
        if ($request->report == 4) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->where('hs_hospitals_history.updated_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were updated this Month";
        }

        // Facilities Updated Last Month
        if ($request->report == 5) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->whereBetween('hs_hospitals_history.updated_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were updated in the last Month";
        }

        // Facilities Updated Last 3 Months
        if ($request->report == 6) {
            $data['facilities'] = DB::table('hs_hospitals_history')
                ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')

                ->select(
                    'hs_hospitals_history.*',
                    'ou_states.name as state_name',
                    'ou_lgas.name as lga_name',
                    'ou_wards.name as ward_name',
                    'lst_ownerships.name as ownership_name',
                    'lst_level_of_care.name as facility_level_name',

                )
                ->whereBetween('hs_hospitals_history.updated_at', [Carbon::now()->subMonths(3), Carbon::now()])
                ->orderBy('hs_hospitals_history.state_id')
                ->orderBy('hs_hospitals_history.lga_id')
                ->orderBy('hs_hospitals_history.ward_id')
                ->orderBy('hs_hospitals_history.facility_name')
                ->get();
            $data['report'] = $data['facilities']->count() . " Facilities were updated in the last 3 Months";
        }

        // If no report type matched, set a default response
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report type selected.',
                'data' => [],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully.',
            'data' => $data,
        ], 200);
    }


    public function searchHospitals2(Request $request)
    {
        // \Log::info($request);
        // Extracting request parameters
        $ward_id = $request->ward_id == 0 ? '' : $request->ward_id;
        $facility_level_id = $request->facility_level_id == 0 ? '' : $request->facility_level_id;
        $ownership_id = $request->ownership_id == 0 ? '' : $request->ownership_id;
        $ownership_type_id = $request->ownership_type_id == 0 ? '' : $request->ownership_type_id;
        $operational_status_id = $request->operational_status_id == 0 ? '' : $request->operational_status_id;
        $registration_status_id = $request->registration_status_id == 0 ? '' : $request->registration_status_id;
        $license_status_id = $request->license_status_id == 0 ? '' : $request->license_status_id;

        // Handling geo_codes conditions (compatible with PHP 7)
        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        } elseif ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        } elseif ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        } else {
            $cond = "<>";
            $value = '';
        }

        // Handling service type conditions
        $outpatient = $request->service_type == 1 ? '' : '';
        $inpatient = $request->service_type == 2 ? '' : '';

        // Handling hospital services filtering
        if (!empty($request->services)) {
            $hospitalIds = DB::table('hs_hospital_services')
                ->whereIn('service_id', $request->services)
                ->distinct()
                ->pluck('hospital_id')
                ->toArray();
        } else {
            $hospitalIds = DB::table('hs_hospitals_history')->pluck('id')->toArray();
        }

        $data['facilities'] = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )

            ->where('hs_hospitals_history.state_id', '=', $request->state_id)
            ->where('hs_hospitals_history.lga_id', '=', $request->lga_id)
            ->where(DB::raw("IFNULL(hs_hospitals_history.ward_id, '')"), '=', $ward_id)

            ->when($ownership_type_id, function ($query) use ($ownership_type_id) {
                $query->where('hs_hospitals_history.ownership_type_id', 'like', '%' . $ownership_type_id . '%');
            })

            ->where('hs_hospitals_history.facility_level_id', 'like', '%' . $facility_level_id . '%')
            // ->where('hs_hospitals_history.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('hs_hospitals_history.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('hs_hospitals_history.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('hs_hospitals_history.license_status_id', 'like', '%' . $license_status_id . '%')
            // ->where(DB::raw("IFNULL(hs_hospitals_history.outpatient, '')"), 'like', '%' . $outpatient . '%')
            // ->where(DB::raw("IFNULL(hs_hospitals_history.inpatient, '')"), 'like', '%' . $inpatient . '%')

            ->where('hs_hospitals_history.facility_name', 'like', '%' . $request->facility_name . '%')

            // Search by Location (State, LGA, or Ward)
            // ->orWhere('ou_states.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_lgas.name', 'like', '%' . $request->facility_name . '%')
            // ->orWhere('ou_wards.name', 'like', '%' . $request->facility_name . '%')


            ->where(DB::raw("IFNULL(hs_hospitals_history.latitude, '')"), $cond, $value)
            ->whereIn('hs_hospitals_history.id', $hospitalIds)

            // Only show approved/published facilities (6 = Created, 13 = Updated) + legacy data (0/NULL)
            ->where(function ($q) {
                $q->whereIn('hs_hospitals_history.status_id', [6, 13])
                  ->orWhereNull('hs_hospitals_history.status_id')
                  ->orWhere('hs_hospitals_history.status_id', 0);
            })

            ->orderBy('hs_hospitals_history.state_id')
            ->orderBy('hs_hospitals_history.lga_id')
            ->orderBy('hs_hospitals_history.ward_id')
            ->orderBy('hs_hospitals_history.facility_name')
            ->paginate(1000000)
            ->appends($request->all());


        // Returning request values
        $data += [
            'state_id' => $request->state_id,
            'lga_id' => $request->lga_id,
            'ward_id' => $request->ward_id,
            'facility_name' => $request->facility_name,
            'geo_codes' => $request->geo_codes,
            'facility_level_id' => $request->facility_level_id,
            'ownership_id' => $request->ownership_id,
            'operational_status_id' => $request->operational_status_id,
            'registration_status_id' => $request->registration_status_id,
            'license_status_id' => $request->license_status_id,
            'service_type' => $request->service_type,
            'service_category_id' => $request->service_category_id,
            'searched' => 1
        ];

        // Returning JSON response
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function searchHospitals3OLD(Request $request)
    {

        // Extracting request parameters
        $facility_level_id = $request->facility_level_id == 0 ? '' : $request->facility_level_id;


        // Handling hospital services filtering
        if (!empty($request->services)) {
            $hospitalIds = DB::table('hs_hospital_services')
                ->whereIn('service_id', $request->services)
                ->distinct()
                ->pluck('hospital_id')
                ->toArray();
        } else {
            $hospitalIds = DB::table('hs_hospitals_history')->pluck('id')->toArray();
        }

        \Log::info($request);

        $data['facilities'] = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )


            ->when($request->facility_level_id, function ($q, $facilityLevel) {
                return $q->where('hs_hospitals_history.facility_level_id', $facilityLevel);
            })
            ->when($request->facility_type_id, function ($q, $facilityType) {
                return $q->where('hs_hospitals_history.facility_type_id', $facilityType);
            })

            ->when($request->facility_name, function ($q, $facilityName) {
                $q->where(function ($subQuery) use ($facilityName) {
                    $subQuery->where('ou_states.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_lgas.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_wards.name', 'like', '%' . $facilityName . '%');
                });
            })

            ->orderBy('hs_hospitals_history.facility_name')
            ->paginate(5000)
            ->appends($request->all());




        // Returning request values
        $data += [
            'state_id' => $request->state_id,
            'lga_id' => $request->lga_id,
            'ward_id' => $request->ward_id,
            'facility_name' => $request->facility_name,
            'geo_codes' => $request->geo_codes,
            'facility_level_id' => $request->facility_level_id,
            'ownership_id' => $request->ownership_id,
            'operational_status_id' => $request->operational_status_id,
            'registration_status_id' => $request->registration_status_id,
            'license_status_id' => $request->license_status_id,
            'service_type' => $request->service_type,
            'service_category_id' => $request->service_category_id,
            'searched' => 1
        ];

        // Returning JSON response
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }



    public function searchHospitals3OLDBYDOCTOR(Request $request)
    {
        $query = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )
            ->when($request->facility_level_id, function ($q, $facilityLevel) {
                return $q->where('hs_hospitals_history.facility_level_id', $facilityLevel);
            })
            ->when($request->facility_type_id, function ($q, $facilityType) {
                return $q->where('hs_hospitals_history.facility_type_id', $facilityType);
            })
            ->when($request->facility_name, function ($q, $facilityName) {
                return $q->where(function ($subQuery) use ($facilityName) {
                    $subQuery->where('ou_states.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_lgas.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_wards.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('hs_hospitals_history.facility_name', 'like', '%' . $facilityName . '%');
                });
            });

        // Log the raw SQL query and bindings for debugging
        // \Log::info($query->toSql());
        // \Log::info($query->getBindings());

        // $data['facilities'] = $query->get();

        // Check if there are any search parameters
        $hasSearchParams = $request->facility_level_id || $request->facility_type_id || $request->facility_name;

        if ($hasSearchParams) {
            $data['facilities'] = $query->get();
        } else {
            $data['facilities'] = $query->paginate(2000);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }


/**
 * Search Hospitals (Latest Version)
 *
 *
 * @bodyParam facility_level_id int Facility level. Example: 2
 * @bodyParam facility_type_id int Facility type. Example: 3
 * @bodyParam facility_name string Facility name search. Example: "General Hospital"
 *
 * @response 200 {
 *   "success": true,
 *   "data": { ... }
 * }
 */
    public function searchHospitals3(Request $request)
    {

        // Log the request parameters
        // \Log::info('Search Hospitals Request Parameters:', $request->all());
        // \Log::info($request->facility_name);

        // Prefer restricting to hospital_details when that relation is usable. On some
        // hosted MySQL imports, hospital_details is a VIEW with a missing DEFINER — any
        // reference 500s; probe first and fall back to hs_hospitals_history filters only.

        $query = DB::table('hs_hospitals_history');

        $hospitalDetailsOk = false;
        if (Schema::hasTable('hospital_details')) {
            try {
                $hospitalDetailsOk = DB::table('hospital_details')->limit(1)->exists();
            } catch (\Throwable $e) {
                Log::warning('searchHospitals3: hospital_details not usable, skipping filter', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if ($hospitalDetailsOk) {
            $query->join('hospital_details', 'hs_hospitals_history.id', '=', 'hospital_details.id');
        }

        $query
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name'
            )
            // Only show published facilities (0 = legacy, 6 = Created, 13 = Updated) + NULL
            ->where(function ($q) {
                $q->whereIn('hs_hospitals_history.status_id', [0, 6, 13])
                  ->orWhereNull('hs_hospitals_history.status_id');
            })
            ->when($request->facility_level_id, function ($q, $facilityLevel) {
                return $q->where('hs_hospitals_history.facility_level_id', $facilityLevel);
            })
            ->when($request->facility_type_id, function ($q, $facilityType) {
                return $q->where('hs_hospitals_history.facility_type_id', (int) $facilityType);
            })

            ->when($request->facility_name, function ($q, $facilityName) {
                $facilityName = trim($facilityName);
                return $q->where(function ($subQuery) use ($facilityName) {
                    $subQuery->where('ou_states.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_lgas.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_wards.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('hs_hospitals_history.facility_name', 'like', '%' . $facilityName . '%');
                });
            });



        // Optimization: Cache initial load for 10 minutes
        if (empty($request->all())) {
            $data = Cache::remember('hfr_facilities_initial', 600, function () use ($query) {
                return ['facilities' => $query->simplePaginate(100)];
            });
        } else {
            $data['facilities'] = $query->simplePaginate(100);
        }


        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }


/**
 * Get Hospital Detail
 *
 *
 * @urlParam facilityId int Required ID of the facility. Example: 123
 *
 * @response 200 {
 *   "success": true,
 *   "data": {
 *       "hospital": { ... }
 *   }
 * }
 */
    public function HospitalDetail(Request $request, $facilityId)
    {
        // \Log::info($facilityId);
        // \Log::info("stop by adams");

        $data['hospital'] = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hs_hospitals_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )

            ->where('hs_hospitals_history.id', $facilityId)

            ->first();

        // Returning JSON response
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }
}
