<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


/**
 * @group Public API (Frontend) - Facility Directory
 *
 * Public-facing APIs for browsing and searching healthcare facilities including hospitals, 
 * pharmacies, laboratories, and imaging centers. No authentication required.
 */
class FacilityListingController extends Controller
{


        /**
     * List All Hospitals
     *
     * Displays a paginated list (20 per page) of all registered hospitals.
     * Results are sorted by state, LGA, ward, and facility name.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.list_hospitals",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state_id": 1,
     *         "lga_id": 5,
     *         "ward_id": 12,
     *         "facility_level_id": 3,
     *         "ownership_id": 1,
     *         "operational_status_id": 1,
     *         "registration_status_id": 1,
     *         "license_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986",
     *         "created_at": "2024-01-15 10:00:00"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 450
     *   },
     *   "data": {
     *     "state_id": 1,
     *     "lga_id": 1,
     *     "ward_id": 0,
     *     "facility_name": "",
     *     "geo_codes": 0,
     *     "facility_level_id": 0,
     *     "ownership_id": 0,
     *     "operational_status_id": 0,
     *     "registration_status_id": 0,
     *     "license_status_id": 0,
     *     "service_type": 0,
     *     "service_category_id": 0,
     *     "searched": 0
     *   }
     * }
     */
    public function getHospitals()
    {

        $facilities = DB::table('hospital_details')
            ->orderBy('state_id')
            ->orderBy('lga_id')
            ->orderBy('ward_id')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 0;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['service_type'] = 0;
        $data['service_category_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_hospitals', compact('facilities', 'data'));
    }



    /**
     * Search Hospitals with Filters
     *
     * Advanced search for hospitals with multiple filter options including location, 
     * facility attributes, services offered, and geographic coordinates availability.
     * Use 0 or empty values to skip specific filters.
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states. Example: 1
     * @bodyParam lga_id integer required LGA ID. Use 1 for all LGAs. Example: 5
     * @bodyParam ward_id integer Ward ID. Use 0 to skip ward filter. Example: 12
     * @bodyParam facility_name string Facility name search term. Partial matching supported. Example: General Hospital
     * @bodyParam geo_codes integer Geographic coordinates filter: 0 (all), 1 (with coordinates), 2 (without coordinates). Example: 1
     * @bodyParam facility_level_id integer Facility level/care level ID. Use 0 to skip. Example: 3
     * @bodyParam ownership_id integer Ownership type ID. Use 0 to skip. Example: 1
     * @bodyParam operational_status_id integer Operational status ID. Use 0 to skip. Example: 1
     * @bodyParam registration_status_id integer Registration status ID. Use 0 to skip. Example: 1
     * @bodyParam license_status_id integer License status ID. Use 0 to skip. Example: 1
     * @bodyParam service_type integer Service type filter: 0 (all), 1 (outpatient only), 2 (inpatient only). Example: 1
     * @bodyParam service_category_id integer Service category ID. Use 0 to skip. Example: 0
     * @bodyParam services array Optional array of service IDs to filter facilities offering specific services. Example: [1, 5, 8]
     *
     * @response 200 scenario="Filtered Results" {
     *   "view": "public.list_hospitals",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state_id": 1,
     *         "lga_id": 5,
     *         "ward_id": 12,
     *         "facility_level_id": 3,
     *         "ownership_id": 1,
     *         "operational_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986",
     *         "outpatient": "Yes",
     *         "inpatient": "Yes"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 45
     *   },
     *   "data": {
     *     "searched": 1
     *   }
     * }
     *
     * @response 200 scenario="No Results" {
     *   "facilities": {
     *     "data": [],
     *     "total": 0
     *   }
     * }
     */
    public function searchHospitals(Request $request)
    {
        // dd($request->all());
        $ward_id = $request->ward_id;
        $facility_level_id = $request->facility_level_id;
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

        if ($request->service_type == 1) {
            $outpatient = 'Yes';
            $inpatient = '';
        } elseif ($request->service_type == 2) {
            $outpatient = '';
            $inpatient = 'Yes';
        } else {
            $outpatient = '';
            $inpatient = '';
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

        if (!empty($request->services)) {
            $hospital = DB::select("SELECT DISTINCT hospital_id FROM hs_hospital_services 
            WHERE service_id IN (" . implode(",", $request->services) . ")");

            $hospital_with_services = [];
            foreach ($hospital as $h) {
                $hospital_with_services[] = $h->hospital_id;
            }
        } else {
            $hospital = DB::select("SELECT id FROM hospital_details");

            $hospital_with_services = [];
            foreach ($hospital as $h) {
                $hospital_with_services[] = $h->id;
            }
        }

        $facilities = DB::table('hospital_details')
            ->where('state_id', 'like', '%' . $request->state_id . '%')
            ->where('lga_id', 'like', '%' . $request->lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->where(DB::Raw("IFNULL(outpatient, '')"), 'like', '%' . $outpatient . '%')
            ->where(DB::Raw("IFNULL(inpatient, '')"), 'like', '%' . $inpatient . '%')
            ->Where('facility_name', 'like', '%' .  $request->facility_name . '%')
            ->where(DB::Raw("IFNULL(latitude, '')"), $cond, $value)
            ->whereIn('id', $hospital_with_services)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('ward')
            ->orderBy('facility_name')
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
        $data['service_type'] = $request->service_type;
        $data['service_category_id'] = $request->service_category_id;
        $data['searched'] = 1;


        return view('public.list_hospitals', compact('facilities', 'data'));
    }




    /**
     * List All Pharmacies
     *
     * Displays a paginated list (20 per page) of all registered pharmacies.
     * Results are sorted by state, LGA, and facility name.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.list_pharmacy",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "unique_id": "PH-FCT-001",
     *         "facility_name": "Alpha Pharmacy Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "registration_status_id": 1,
     *         "license_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 280
     *   }
     * }
     */
    public function getPharmacy()
    {

        $facilities = DB::table('pharmacy_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_pharmacy', compact('facilities', 'data'));
    }



     /**
     * Search Pharmacies with Filters
     *
     * Advanced search for pharmacies with filters for location, ownership, 
     * operational status, and geographic coordinates availability.
     * Use 0 or empty values to skip specific filters.
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states. Example: 1
     * @bodyParam lga_id integer required LGA ID. Use 1 for all LGAs. Example: 5
     * @bodyParam ward_id integer Ward ID. Use 0 to skip ward filter. Example: 12
     * @bodyParam facility_name string Pharmacy name search term. Partial matching supported. Example: Alpha Pharmacy
     * @bodyParam geo_codes integer Geographic coordinates filter: 0 (all), 1 (with coordinates), 2 (without coordinates). Example: 1
     * @bodyParam ownership_id integer Ownership type ID. Use 0 to skip. Example: 2
     * @bodyParam operational_status_id integer Operational status ID. Use 0 to skip. Example: 1
     * @bodyParam registration_status_id integer Registration status ID. Use 0 to skip. Example: 1
     * @bodyParam license_status_id integer License status ID. Use 0 to skip. Example: 1
     *
     * @response 200 scenario="Filtered Results" {
     *   "view": "public.list_pharmacy",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "facility_name": "Alpha Pharmacy Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 25
     *   },
     *   "data": {
     *     "searched": 1
     *   }
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



        $facilities = DB::table('pharmacy_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20)
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


        return view('public.list_pharmacy', compact('facilities', 'data'));
    }



    /**
     * List All Laboratories
     *
     * Displays a paginated list (20 per page) of all registered medical laboratories.
     * Results are sorted by state, LGA, and facility name.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.list_labs",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "unique_id": "LAB-FCT-001",
     *         "facility_name": "Central Diagnostic Lab",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "facility_level_id": 2,
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "registration_status_id": 1,
     *         "license_status_id": 1,
     *         "accreditation_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 180
     *   }
     * }
     */
    public function getLab()
    {

        $facilities = DB::table('laboratory_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['accreditation_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_labs', compact('facilities', 'data'));
    }



 /**
     * Search Laboratories with Filters
     *
     * Advanced search for laboratories with filters for location, facility level, 
     * ownership, operational status, and accreditation status.
     * Use 0 or empty values to skip specific filters.
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states. Example: 1
     * @bodyParam lga_id integer required LGA ID. Use 1 for all LGAs. Example: 5
     * @bodyParam ward_id integer Ward ID. Use 0 to skip ward filter. Example: 12
     * @bodyParam facility_name string Laboratory name search term. Partial matching supported. Example: Central Diagnostic
     * @bodyParam geo_codes integer Geographic coordinates filter: 0 (all), 1 (with coordinates), 2 (without coordinates). Example: 1
     * @bodyParam facility_level_id integer Facility level ID. Use 0 to skip. Example: 2
     * @bodyParam ownership_id integer Ownership type ID. Use 0 to skip. Example: 2
     * @bodyParam operational_status_id integer Operational status ID. Use 0 to skip. Example: 1
     * @bodyParam registration_status_id integer Registration status ID. Use 0 to skip. Example: 1
     * @bodyParam license_status_id integer License status ID. Use 0 to skip. Example: 1
     * @bodyParam accreditation_status_id integer Accreditation status ID. Use 0 to skip. Example: 1
     *
     * @response 200 scenario="Filtered Results" {
     *   "view": "public.list_labs",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "facility_name": "Central Diagnostic Lab",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "facility_level_id": 2,
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "accreditation_status_id": 1,
     *         "latitude": "9.0765"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 15
     *   },
     *   "data": {
     *     "searched": 1
     *   }
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

        $facilities = DB::table('laboratory_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('accreditation_status_id', 'like', '%' . $accreditation_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
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

        return view('public.list_labs', compact('facilities', 'data'));
    }



      /**
     * List All Imaging Centers
     *
     * Displays a paginated list (20 per page) of all registered imaging/radiology centers.
     * Results are sorted by state, LGA, and facility name.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.list_imaging",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "unique_id": "IMG-FCT-001",
     *         "facility_name": "Premier Imaging Center",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "registration_status_id": 1,
     *         "license_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 120
     *   }
     * }
     */
    public function getImaging()
    {

        $facilities = DB::table('imaging_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_imaging', compact('facilities', 'data'));
    }



    /**
     * Search Imaging Centers with Filters
     *
     * Advanced search for imaging/radiology centers with filters for location, 
     * ownership, operational status, and geographic coordinates availability.
     * Use 0 or empty values to skip specific filters.
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states. Example: 1
     * @bodyParam lga_id integer required LGA ID. Use 1 for all LGAs. Example: 5
     * @bodyParam ward_id integer Ward ID. Use 0 to skip ward filter. Example: 12
     * @bodyParam facility_name string Imaging center name search term. Partial matching supported. Example: Premier Imaging
     * @bodyParam geo_codes integer Geographic coordinates filter: 0 (all), 1 (with coordinates), 2 (without coordinates). Example: 1
     * @bodyParam ownership_id integer Ownership type ID. Use 0 to skip. Example: 2
     * @bodyParam operational_status_id integer Operational status ID. Use 0 to skip. Example: 1
     * @bodyParam registration_status_id integer Registration status ID. Use 0 to skip. Example: 1
     * @bodyParam license_status_id integer License status ID. Use 0 to skip. Example: 1
     *
     * @response 200 scenario="Filtered Results" {
     *   "view": "public.list_imaging",
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "facility_name": "Premier Imaging Center",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ownership_id": 2,
     *         "operational_status_id": 1,
     *         "latitude": "9.0765",
     *         "longitude": "7.3986"
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 8
     *   },
     *   "data": {
     *     "searched": 1
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



        $facilities = DB::table('imaging_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20)
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


        return view('public.list_imaging', compact('facilities', 'data'));
    }



 

        /**
     * Get Facility Updates Report
     *
     * Retrieves facilities that were created or updated within predefined time periods.
     * Useful for tracking recent changes to the facility registry.
     * This endpoint is publicly accessible without authentication.
     *
     * @queryParam report integer required Report type:
     * - 1: Facilities created this month
     * - 2: Facilities created last month
     * - 3: Facilities created in last 3 months
     * - 4: Facilities updated this month
     * - 5: Facilities updated last month
     * - 6: Facilities updated in last 3 months
     * Example: 1
     *
     * @response 200 scenario="New Facilities This Month" {
     *   "view": "public.facilities_updates",
     *   "facilities": [
     *     {
     *       "id": 123,
     *       "unique_id": "FCT-123",
     *       "facility_name": "New Hospital Abuja",
     *       "state": "FCT",
     *       "lga": "Abuja Municipal",
     *       "ward": "Garki",
     *       "created_at": "2025-11-10 10:00:00",
     *       "updated_at": "2025-11-10 10:00:00"
     *     }
     *   ],
     *   "report": "15 Facilities were created this Month"
     * }
     *
     * @response 200 scenario="Updated Facilities Last Month" {
     *   "facilities": [
     *     {
     *       "id": 45,
     *       "facility_name": "General Hospital Abuja",
     *       "updated_at": "2025-10-25 14:30:00"
     *     }
     *   ],
     *   "report": "28 Facilities were updated in the last Month"
     * }
     *
     * @response 200 scenario="No Facilities Found" {
     *   "facilities": [],
     *   "report": "0 Facilities were created this Month"
     * }
     */
    public function getUpdates(Request $request)
    {

        //New Facilities Created This Month
        if ($request->report == 1) {
            $facilities = DB::table('hospital_details')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created this Month";
        }

        //New Facilities Created Last Month
        if ($request->report == 2) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created in the last Month";
        }

        //New Facilities Created Last 3 Months
        if ($request->report == 3) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created in the last 3 Month";
        }

        //Facilities Updated This Month 
        if ($request->report == 4) {
            $facilities = DB::table('hospital_details')
                ->where('updated_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();

            $report = $facilities->count() . " Facilities were updated this Month";
        }

        //Facilities Updated Last Month
        if ($request->report == 5) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were updated in the last Month";
        }

        //Facilities Updated Last 3 Month
        if ($request->report == 6) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were updated in the last 3 Month";
        }


        return view('public.facilities_updates', compact('facilities', 'report'));
    }



   /**
     * Display Facility Updates Selection Page
     *
     * Shows the initial page for viewing facility updates/changes reports.
     * User can select from predefined time periods to view new or updated facilities.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.facilities_updates",
     *   "facilities": "none"
     * }
     */
    public function updates()
    {
        $facilities = "none";

        return view('public.facilities_updates', compact('facilities'));
    }
}
