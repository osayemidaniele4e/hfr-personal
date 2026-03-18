<?php

namespace App\Http\Controllers\Frontend\API;

use App\Download;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DownloadVerificationCode;
use App\Website\API\Origin;
use App\Website\API\Process;
use App\Website\API\ProcessItem;
use App\Website\API\Slider;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Notification;

// use Notification;
use App\Notifications\SendDownloadVerificationCode;
use App\Resource;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    public function slider(): JsonResponse
    {
        $sliders = Slider::all();
        return response()->json([
            'success' => true,
            'data' => $sliders
        ], 200);
    }

    public function processItem(): JsonResponse
    {
        $processItems = ProcessItem::all();
        return response()->json([
            'success' => true,
            'data' => $processItems
        ], 200);
    }


    public function origin(): JsonResponse
    {
        $origin = Origin::where('id', 1)->first();
        return response()->json([
            'success' => true,
            'data' => $origin
        ], 200);
    }


    public function process(): JsonResponse
    {
        $process = Process::where('id', 1)->first();
        return response()->json([
            'success' => true,
            'data' => $process
        ], 200);
    }


    public function facilityType(): JsonResponse
    {
        $results = DB::table('lst_facility_types')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }

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

    public function states(): JsonResponse
    {
        $results =  DB::table('ou_states')
            ->select('id', 'name')
            ->orderByRaw('name ASC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }

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

    public function getServiceCategory(Request $request): JsonResponse
    {
        $results = DB::table('lst_hosp_service_category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 200);
    }

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

    // public function searchHospitals(Request $request)
    // {
    //     \Log::info($request);
    //     \Log::info("Adams");

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


    // new implementation of searchHospitals with simplified code and better handling of request parameters
public function searchHospitals(Request $request)
{
    // We are forcing the query to find one specific record from the 766 'missing' ones
    // Name: Unale Primary Health Care Centre (ID: 87640022)
    $query = DB::table('hs_hospitals_history')
        ->where('id', '87640022') 
        ->select('id', 'facility_name', 'state_id', 'status_id');

    $facilities = $query->paginate(1);

    // Maintaining your exact required format
    $data = [
        'facilities' => $facilities,
        'state_id' => $request->state_id,
        'searched' => 1,
        'test_mode' => 'If you see this, the correct function is being called'
    ];

    return response()->json([
        'success' => true,
        'data' => $data
    ], 200);
}
    public function getFacilitesByLGA(Request $request)
    {
        $total_facilities_lga = DB::select("SELECT l.map_code LGA_UID,count(h.id) value
                    FROM hs_hospitals h
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
            // 'g-recaptcha-response' => 'required|captcha',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        \Log::info($request);
        try {
            // Store data in the database
            $download = Download::create($request->all());

            // Generate verification code
            $code = $this->generateToken();

            // Store token in session (if needed)
            session()->put('download_verify', [
                'token' => $code,
                'expire_at' => now()->addMinutes(15),
            ]);

            // Send verification email
            if (!$this->sendToken2Email($request->email, $code)) {
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

    public function sendToken2Email($email, $code)
    {

        try {
            Mail::to($email)->send(new DownloadVerificationCode($code));
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


    public function getUpdates(Request $request)
    {
        $data = []; // Initialize $data to prevent "Undefined variable" error

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

    public function searchHospitals3(Request $request)
    {

        // Log the request parameters
        // \Log::info('Search Hospitals Request Parameters:', $request->all());
        // \Log::info($request->facility_name);
        $query = DB::table('hs_hospitals_history')
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
            ->when($request->facility_level_id, function ($q, $facilityLevel) {
                return $q->where('hs_hospitals_history.facility_level_id', $facilityLevel);
            })
            ->when($request->facility_type_id, function ($q, $facilityType) {
                return $q->where('hs_hospitals_history.facility_type_id', (int) $facilityType);
            })

            ->when($request->facility_name, function ($q, $facilityName) {
                $facilityName = trim($facilityName); // 👈 Trim the input
                return $q->where(function ($subQuery) use ($facilityName) {
                    $subQuery->where('ou_states.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_lgas.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('ou_wards.name', 'like', '%' . $facilityName . '%')
                        ->orWhere('hs_hospitals_history.facility_name', 'like', '%' . $facilityName . '%');
                });
            });



        $data['facilities'] = $query->paginate(2000);


        // Access the facilities data from the paginator
        $facilities = $data['facilities']->items();  // Get the facilities as an array

        // Initialize an empty array to store counts of facilities per state
        // Initialize variable to hold the highest facility
        $highestFacility = null;

        // Loop through each facility to determine the highest based on facility level (or any other criteria)
        foreach ($facilities as $facility) {
            if (!$highestFacility || $facility->facility_level_id > $highestFacility->facility_level_id) {
                $highestFacility = $facility;  // Update the highest facility if current one has a higher facility level
            }
        }

        // Log the highest facility
        // \Log::info('Highest Facility:', (array)$highestFacility);


        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

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
