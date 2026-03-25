<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Exports\HFExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DateTime;
use Illuminate\Support\Facades\Log;


/**
 * @group Administration - Facility Reports
 *
 * APIs for generating and exporting facility reports including updates, services, status, and approver summaries.
 */
class FacilityReportController extends Controller
{

 /**
     * Display Facility Updates Report Form
     *
     * Shows the initial form for generating facility updates reports with date range and report type filters.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "view": "reports.facility_list_updates",
     *   "facilities": "none",
     *   "data": {
     *     "from": "",
     *     "to": "",
     *     "report": ""
     *   }
     * }
     */
    public function updateSelection()
    {
        $facilities = "none";

        $data['from'] = "";
        $data['to'] = "";
        $data['report'] = "";

        return view('reports.facility_list_updates', compact('facilities', 'data'));
    }



 /**
     * Generate Facility Updates Report
     *
     * Generates a comprehensive report of facilities based on the selected report type:
     * - Type 1: Newly created facilities
     * - Type 2: Updated facilities
     * - Type 3: Deleted facilities
     *
     * Results are filtered by the authenticated user's state and cached for download.
     *
     * @authenticated
     *
     * @bodyParam from_date string required Start date in format DD-MM-YYYY or YYYY-MM-DD. Example: 01-01-2025
     * @bodyParam to_date string required End date in format DD-MM-YYYY or YYYY-MM-DD. Example: 31-01-2025
     * @bodyParam report integer required Report type: 1 (New), 2 (Updated), 3 (Deleted). Example: 1
     *
     * @response 200 scenario="New Facilities Report" {
     *   "message": "15 New facilities were created between 01 Jan 2025 and 31 Jan 2025",
     *   "facilities": [
     *     {
     *       "unique_id": "FCT-001",
     *       "facility_name": "General Hospital Abuja",
     *       "state": "FCT",
     *       "lga": "Abuja Municipal",
     *       "ward": "Garki",
     *       "ownership": "Public",
     *       "facility_level": "Secondary",
     *       "operational_status": "Operational",
     *       "registration_status": "Registered",
     *       "license_status": "Licensed",
     *       "created_at": "2025-01-15 10:30:00"
     *     }
     *   ]
     * }
     *
     * @response 200 scenario="Updated Facilities Report" {
     *   "message": "23 Facilities were updated between 01 Jan 2025 and 31 Jan 2025"
     * }
     *
     * @response 200 scenario="Deleted Facilities Report" {
     *   "message": "5 Facilities were deleted between 01 Jan 2025 and 31 Jan 2025"
     * }
     */
    public function getUpdatesReport(Request $request)
    {
        $from = date('Y-m-d', strtotime(str_replace('-', '/', $request->from_date)));
        $to = date('Y-m-d', strtotime(str_replace('-', '/', $request->to_date)));
        $to_date = new DateTime($to);
        $to_date->modify('+1 day');

        //New Facilities
        if ($request->report == 1) {
            $facilities = DB::table('hospital_details')

                ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
                ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
                ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_oparational_status', 'hospital_details.operational_status_id', '=', 'lst_oparational_status.id')
                ->leftJoin('lst_registration_status', 'hospital_details.registration_status_id', '=', 'lst_registration_status.id')
                ->leftJoin('lst_license_status', 'hospital_details.license_status_id', '=', 'lst_license_status.id')
                ->select(
                    'hospital_details.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_ownerships.name as ownership',
                    'lst_oparational_status.status as operational_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status'
                )

                ->whereBetween('hospital_details.created_at', [$from, $to_date])
                ->Where('hospital_details.state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->orderBy('hospital_details.created_at')
                ->get();

            $message = $facilities->count() . " New facilities were created between " . date('d M Y', strtotime($from)) . " and " . date('d M Y', strtotime($to));
        }

        //Updated Facilities
        if ($request->report == 2) {
            $facilities = DB::table('hospital_details')
                ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
                ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
                ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
                ->leftJoin('lst_oparational_status', 'hospital_details.operational_status_id', '=', 'lst_oparational_status.id')
                ->leftJoin('lst_registration_status', 'hospital_details.registration_status_id', '=', 'lst_registration_status.id')
                ->leftJoin('lst_license_status', 'hospital_details.license_status_id', '=', 'lst_license_status.id')
                ->select(
                    'hospital_details.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_ownerships.name as ownership',
                    'lst_oparational_status.status as operational_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status'
                )
                ->whereBetween('hospital_details.updated_at', [$from, $to_date])
                ->Where('hospital_details.state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->orderBy('hospital_details.updated_at')
                ->get();

            $message = $facilities->count() . " Facilities were updated between " . date('d M Y', strtotime($from)) . " and " . date('d M Y', strtotime($to));
        }

        //Deleted Facilities
        if ($request->report == 3) {
            $facilities = DB::table('hs_hospitals_history')
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
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_ownerships.name as ownership',
                    'lst_oparational_status.status as operational_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status'
                )
                ->whereBetween('hs_hospitals_history.updated_at', [$from, $to_date])
                ->Where('hs_hospitals_history.state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->where('hs_hospitals_history.status_id', 20)
                ->orderBy('hs_hospitals_history.updated_at')
                ->get();

            $message = $facilities->count() . " Facilities were deleted between " . date('d M Y', strtotime($from)) . " and " . date('d M Y', strtotime($to));
        }

        Cache::put('facilities_updates_download', $facilities, 60);

        $data['from'] = $request->from_date;
        $data['to'] = $request->to_date;
        $data['report'] = $request->report;
        $data['message'] = $message;

        return view('reports.facility_list_updates', compact('facilities', 'data'));
    }


/**
     * Download Facility Updates Report
     *
     * Downloads the previously generated facility updates report as an Excel file.
     * Report data must be generated first using the getUpdatesReport endpoint.
     * The cached report expires after 60 minutes.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "content-type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *   "filename": "data.xlsx",
     *   "description": "Excel file with columns: unique_id, reg_number, start_date, facility_name, alt_facility_name, state, lga, ward, ownership, facility_level, longitude, latitude, operation_status, registration_status, license_status, date"
     * }
     *
     * @response 404 scenario="No Cached Report" {
     *   "message": "No report data available. Please generate a report first."
     * }
     */
    public function updatesDownload()
    {

        if (Cache::has('facilities_updates_download')) {
            $facilities = Cache::get('facilities_updates_download');
            // dd($facilities);
            $column_header = [
                "unique_id",
                "reg_number",
                "start_date",
                "facility_name",
                "alt_facility_name",
                "state",
                "lga",
                "ward",
                "ownership",
                "facility_level",
                "longitude",
                "latitude",
                "operation_status",
                "registration_status",
                "license_status",
                "date"
            ];

            // Key mapping from your desired header to the actual field in data
            $field_mapping = [
                "unique_id" => "unique_id",
                "reg_number" => "registration_no",
                "start_date" => "start_date",
                "facility_name" => "facility_name",
                "alt_facility_name" => "alt_facility_name",
                "state" => "state",
                "lga" => "lga",
                "ward" => "ward",
                "ownership" => "ownership",
                "facility_level" => "facility_level",
                "longitude" => "longitude",
                "latitude" => "latitude",
                "operation_status" => "operational_status",
                "registration_status" => "registration_status",
                "license_status" => "license_status",
                "date" => "created_at"
            ];

            $filteredFacilities = $facilities->map(function ($facility) use ($column_header, $field_mapping) {
                return collect($column_header)->mapWithKeys(function ($headerKey) use ($facility, $field_mapping) {
                    $actualKey = $field_mapping[$headerKey];

                    // Format 'created_at' to date only
                    $value = $facility->$actualKey ?? null;

                    if ($actualKey === 'created_at' && $value) {
                        $value = \Carbon\Carbon::parse($value)->toDateString(); // formats to 'YYYY-MM-DD'
                    }

                    return [$headerKey => $value];
                });
            });

            return Excel::download(new HFExport($filteredFacilities->toArray(), $column_header), "data.xlsx");
        }
    }


/**
     * List Facility Services
     *
     * Displays a paginated list (15 per page) of all facility services including beds, 
     * outpatient/inpatient services, laboratory, imaging, pharmacy, mortuary, and ambulance services.
     * Results are cached for potential export and sorted by state, lga, ward, and facility name.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership": "Public",
     *         "facility_level": "Secondary",
     *         "beds": 150,
     *         "outpatient": "Yes",
     *         "inpatient": "Yes",
     *         "onsite_laboratory": "Yes",
     *         "onsite_imaging": "Yes",
     *         "onsite_pharmarcy": "Yes",
     *         "mortuary_services": "No",
     *         "ambulance_services": "Yes",
     *         "services": "Emergency, Surgery, Pediatrics"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 150
     *   }
     * }
     */
    public function servicesIndex()
    {
        // dd(555);
        $facilities = DB::table('hospital_offered_services as hos')
            ->leftJoin('ou_states as s', 'hos.state_id', '=', 's.id')
            ->leftJoin('ou_lgas as l', 'hos.lga_id', '=', 'l.id')
            ->leftJoin('ou_wards as w', 'hos.ward_id', '=', 'w.id')
            ->leftJoin('lst_ownerships as o', 'hos.ownership_id', '=', 'o.id')
            ->leftJoin('lst_level_of_care as f', 'hos.facility_level_id', '=', 'f.id')
            ->select(
                'hos.unique_id',
                'hos.facility_name',
                's.name as state',
                'l.name as lga',
                'w.name as ward',
                'o.name as ownership',
                'f.name as facility_level',
                'hos.beds',
                'hos.outpatient',
                'hos.inpatient',
                'hos.onsite_laboratory',
                'hos.onsite_imaging',
                'hos.onsite_pharmarcy',
                'hos.mortuary_services',
                'hos.ambulance_services',
                'hos.services'
            )
            ->orderBy('s.name')
            ->orderBy('l.name')
            ->orderBy('w.name')
            ->orderBy('hos.facility_name')
            ->paginate(15);


        Cache::put('facilities_services_download', $facilities, 60);

        $data['state_id'] = 1;
        $data['lga_id'] = 0;
        $data['ward_id'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;

        return view('reports.facility_services', compact('facilities', 'data'));
    }


/**
     * Filter Facility Services Report
     *
     * Filters facility services by location (state, lga, ward) and facility attributes 
     * (facility level, ownership). Uses LIKE matching to support flexible filtering.
     * Pass 0 or empty string to skip filtering on a particular field.
     *
     * @authenticated
     *
     * @bodyParam state_id integer State ID. Use 0 to skip state filter. Example: 1
     * @bodyParam lga_id integer LGA ID. Use 0 to skip LGA filter. Example: 10
     * @bodyParam ward_id integer Ward ID. Use 0 to skip ward filter. Example: 50
     * @bodyParam facility_level_id integer Level of care ID. Use 0 to skip level filter. Example: 3
     * @bodyParam ownership_id integer Ownership ID. Use 0 to skip ownership filter. Example: 2
     *
     * @response 200 scenario="Filtered Results" {
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership": "Public",
     *         "facility_level": "Secondary",
     *         "beds": 150,
     *         "outpatient": "Yes",
     *         "inpatient": "Yes",
     *         "onsite_laboratory": "Yes",
     *         "onsite_imaging": "Yes",
     *         "onsite_pharmarcy": "Yes",
     *         "mortuary_services": "No",
     *         "ambulance_services": "Yes",
     *         "services": "Emergency, Surgery, Pediatrics"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 45
     *   }
     * }
     */
    public function getServicesReport(Request $request)
    {
        // $facilities = DB::table('hospital_offered_services')
        //     ->select(
        //         'unique_id',
        //         'facility_name',
        //         'state',
        //         'lga',
        //         'ward',
        //         'ownership',
        //         'facility_level',
        //         'beds',
        //         'outpatient',
        //         'inpatient',
        //         'onsite_laboratory',
        //         'onsite_imaging',
        //         'onsite_pharmarcy',
        //         'mortuary_services',
        //         'ambulance_services',
        //         'services'
        //     )

        $facilities = DB::table('hospital_offered_services as hos')
            ->leftJoin('ou_states as s', 'hos.state_id', '=', 's.id')
            ->leftJoin('ou_lgas as l', 'hos.lga_id', '=', 'l.id')
            ->leftJoin('ou_wards as w', 'hos.ward_id', '=', 'w.id')
            ->leftJoin('lst_ownerships as o', 'hos.ownership_id', '=', 'o.id')
            ->leftJoin('lst_level_of_care as f', 'hos.facility_level_id', '=', 'f.id')
            ->select(
                'hos.unique_id',
                'hos.facility_name',
                's.name as state',
                'l.name as lga',
                'w.name as ward',
                'o.name as ownership',
                'f.name as facility_level',
                'hos.beds',
                'hos.outpatient',
                'hos.inpatient',
                'hos.onsite_laboratory',
                'hos.onsite_imaging',
                'hos.onsite_pharmarcy',
                'hos.mortuary_services',
                'hos.ambulance_services',
                'hos.services'
            )
            ->where('hos.state_id', 'like', '%' . $request->state_id . '%')
            ->where('hos.lga_id', 'like', '%' . $request->lga_id . '%')
            ->where(DB::Raw("IFNULL(hos.ward_id, '')"), 'like', '%' . $request->ward_id . '%')
            ->where('hos.facility_level_id', 'like', '%' . $request->facility_level_id . '%')
            ->where('hos.ownership_id', 'like', '%' . $request->ownership_id . '%')
            ->paginate(15);

        Cache::put('facilities_services_download', $facilities, 60);

        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_level_id'] = $request->facility_level_id;
        $data['ownership_id'] = $request->ownership_id;

        return view('reports.facility_services', compact('facilities', 'data'));
    }


 /**
     * Download Facility Services Report
     *
     * Downloads the filtered facility services report as an Excel file.
     * Report data must be generated/filtered first using servicesIndex or getServicesReport.
     * The cached report expires after 60 minutes.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "content-type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *   "filename": "data.xlsx",
     *   "description": "Excel file with columns: Facility_id, facility_name, state, lga, ward, ownership, facility_level, number_of_beds, outpatient_services, inpatient_services, onsite_laboratory, onsite_imaging, onsite_pharmarcy, mortuary_services, ambulance_services, service_rendered"
     * }
     *
     * @response 404 scenario="No Cached Report" {
     *   "message": "No report data available. Please generate a report first."
     * }
     */
    public function servicesDownload()
    {

        if (Cache::has('facilities_services_download')) {
            $facilities = Cache::get('facilities_services_download');

            $column_header = array(
                "Facility_id",
                "facility_name",
                "state",
                "lga",
                "ward",
                "ownership",
                "facility_level",
                "number_of_beds",
                "outpatient_services",
                "inpatient_services",
                "onsite_laboratory",
                "onsite_imaging",
                "onsite_pharmarcy",
                "mortuary_services",
                "ambulance_services",
                "service_rendered"
            );

            return Excel::download(new HFExport($facilities->all(), $column_header), "data.xlsx");
        }
    }


 /**
     * Display Facility Status Summary
     *
     * Displays a summary of facility statuses aggregated at the state level.
     * Shows counts for various workflow stages: requested, verified, validated, created, updated, 
     * deleted, and rejected facilities.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "facility_status": [
     *     {
     *       "state": "FCT",
     *       "New_Facility_Requested": 25,
     *       "Update_Requested": 15,
     *       "Deletion_Requested": 3,
     *       "Request_Verified": 20,
     *       "Request_Validated": 18,
     *       "Facility_Created": 18,
     *       "Facility_Updated": 12,
     *       "Facility_Deleted": 2,
     *       "Verification_Rejected": 5,
     *       "Validation_Rejected": 2,
     *       "Publishing_Rejected": 1
     *     }
     *   ],
     *   "data": {
     *     "state_id": 1
     *   }
     * }
     */
    public function statusIndex()
    {
        $facility_status = DB::table('facility_status_state_pivot')->get();

        $data['state_id'] = 1;

        return view('reports.facility_status', compact('facility_status', 'data'));
    }


 /**
     * Get Facility Status Report by State
     *
     * Retrieves facility status summary for a specific state or all states.
     * - state_id = 1: Returns aggregated data for all states
     * - state_id > 1: Returns LGA-level breakdown for the specified state
     *
     * @authenticated
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states, or specific state ID for LGA breakdown. Example: 1
     *
     * @response 200 scenario="All States Summary" {
     *   "facility_status": [
     *     {
     *       "state": "FCT",
     *       "New_Facility_Requested": 25,
     *       "Update_Requested": 15,
     *       "Deletion_Requested": 3,
     *       "Request_Verified": 20,
     *       "Request_Validated": 18,
     *       "Facility_Created": 18,
     *       "Facility_Updated": 12,
     *       "Facility_Deleted": 2,
     *       "Verification_Rejected": 5,
     *       "Validation_Rejected": 2,
     *       "Publishing_Rejected": 1
     *     }
     *   ]
     * }
     *
     * @response 200 scenario="Single State LGA Breakdown" {
     *   "facility_status": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "New_Facility_Requested": 10,
     *       "Update_Requested": 5,
     *       "Deletion_Requested": 1,
     *       "Request_Verified": 8,
     *       "Request_Validated": 7,
     *       "Facility_Created": 7,
     *       "Facility_Updated": 4,
     *       "Facility_Deleted": 1,
     *       "Verification_Rejected": 2,
     *       "Validation_Rejected": 1,
     *       "Publishing_Rejected": 0
     *     }
     *   ]
     * }
     */

    public function getStatusReport(Request $request)
    {
        if ($request->state_id == 1) {
            Log::info('Fetching facility status for all states');


            $facility_status = DB::table('facility_status_state_pivot')
                ->get();
        } else {
            Log::info('Fetching facility status for state ID: ' . $request->state_id);
            $facility_status = DB::table('facility_status_lga_pivot')
                ->where('state_id', $request->state_id)
                // ->where('state_id', 'like', '%' . $request->state_id . '%')
                ->get();
        }

        $data['state_id'] = $request->state_id;

        // Log::info('Facility status data fetched successfully');
        // Log::info('Facility status data: ', (array) $facility_status);
        // dd($facility_status);

        return view('reports.facility_status', compact('facility_status', 'data'));
    }


    /**
     * Download Facility Status Report
     *
     * Downloads the facility status summary as an Excel file.
     * Export format depends on the state_id parameter:
     * - state_id = 1: State-level summary
     * - state_id > 1: LGA-level breakdown for the specified state
     *
     * @authenticated
     *
     * @queryParam state integer required State ID. Use 1 for all states summary, or specific state ID for LGA breakdown. Example: 1
     *
     * @response 200 scenario="Success" {
     *   "content-type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *   "filename": "data.xlsx",
     *   "description": "Excel file with status columns for each workflow stage"
     * }
     */
    public function statusDownload(Request $request)
    {

        if ($request->state_id == 1) {
            $facility_status = DB::table('facility_status_state_pivot')
                ->select(
                    'state',
                    'New_Facility_Requested',
                    'Update_Requested',
                    'Deletion_Requested',
                    'Request_Verified',
                    'Request_Validated',
                    'Facility_Created',
                    'Facility_Updated',
                    'Facility_Deleted',
                    'Verification_Rejected',
                    'Validation_Rejected',
                    'Publishing_Rejected'
                )
                ->get();

            $column_header = array(
                'lga',
                'New Facility Requested',
                'Update Requested',
                'Deletion Requested',
                'Request Verified',
                'Request Validated',
                'Facility Created',
                'Facility Updated',
                'Facility Deleted',
                'Verification Rejected',
                'Validation Rejected',
                'Publishing Rejected'
            );
        } else {
            $facility_status = DB::table('facility_status_lga_pivot')
                ->select(
                    'lga',
                    'New_Facility_Requested',
                    'Update_Requested',
                    'Deletion_Requested',
                    'Request_Verified',
                    'Request_Validated',
                    'Facility_Created',
                    'Facility_Updated',
                    'Facility_Deleted',
                    'Verification_Rejected',
                    'Validation_Rejected',
                    'Publishing_Rejected'
                )
                ->where('state_id', 'like', '%' . $request->state_id . '%')
                ->get();

            $column_header = array(
                'lga',
                'New Facility Requested',
                'Update Requested',
                'Deletion Requested',
                'Request Verified',
                'Request Validated',
                'Facility Created',
                'Facility Updated',
                'Facility Deleted',
                'Verification Rejected',
                'Validation Rejected',
                'Publishing Rejected'
            );
        }

        return Excel::download(new HFExport($facility_status, $column_header), "data.xlsx");
    }



        /**
     * Display Approvers Summary Report
     *
     * Shows a summary of facility approvals grouped by approver (publisher).
     * Displays the count of facilities published by each user.
     * Defaults to showing published facilities (level 3).
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "summary": [
     *     {
     *       "published_by": "john.doe@example.com",
     *       "total": 45
     *     },
     *     {
     *       "published_by": "jane.smith@example.com",
     *       "total": 32
     *     }
     *   ],
     *   "data": {
     *     "state_id": 1,
     *     "level": 3
     *   }
     * }
     */
    public function approversIndex(Request $request)
    {
        $summary = DB::table('hospital_details_history')
            ->select(DB::raw('published_by,count(*) as total'))
            ->where('published_by', '<>', '')
            ->groupBy('published_by')
            ->get();

        $data['state_id'] = 1;
        $data['level'] = 3;

        return view('reports.approvers_summary', compact('summary', 'data'));
    }


     /**
     * Get Approvers Summary by Level and State
     *
     * Retrieves approval summary filtered by approval level and state.
     * - Level 2: Validators (validated_by field)
     * - Level 3: Publishers (published_by field, default)
     *
     * @authenticated
     *
     * @bodyParam state_id integer required State ID. Use 1 for all states. Example: 1
     * @bodyParam level integer required Approval level: 2 (Validators) or 3 (Publishers). Example: 3
     *
     * @response 200 scenario="Publishers Summary" {
     *   "summary": [
     *     {
     *       "published_by": "john.doe@example.com",
     *       "total": 45
     *     }
     *   ],
     *   "data": {
     *     "state_id": 1,
     *     "level": 3
     *   }
     * }
     *
     * @response 200 scenario="Validators Summary" {
     *   "summary": [
     *     {
     *       "validated_by": "validator@example.com",
     *       "total": 38
     *     }
     *   ],
     *   "data": {
     *     "state_id": 1,
     *     "level": 2
     *   }
     * }
     */
    public function approversSummary(Request $request)
    {

        if ($request->level == 2) {
            $summary = DB::table('hospital_details_history')
                ->select(DB::raw('validated_by,count(*) as total'))
                ->where('validated_by', '<>', '')
                ->where('state_id', 'like', '%' . $request->state_id . '%')
                ->groupBy('validated_by')
                ->get();
        } else {

            $summary = DB::table('hospital_details_history')
                ->select(DB::raw('published_by,count(*) as total'))
                ->where('published_by', '<>', '')
                ->where('state_id', 'like', '%' . $request->state_id . '%')
                ->groupBy('published_by')
                ->get();
        }

        $data['state_id'] = $request->state_id;
        $data['level'] = $request->level;

        return view('reports.approvers_summary', compact('summary', 'data'));
    }



       /**
     * Display Facility Status Details Report
     *
     * Shows a paginated list of facilities that have never been updated (status_id = 0).
     * Filtered by the authenticated user's state.
     * Returns 15 facilities per page with complete facility and location details.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 123,
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership": "Public",
     *         "facility_level": "Secondary",
     *         "operational_status": "Operational",
     *         "registration_status": "Registered",
     *         "license_status": "Licensed",
     *         "status": "Never Updated",
     *         "created_at": "2023-01-15 10:30:00"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 45
     *   },
     *   "message": "45 facilities has never been updated"
     * }
     */
    //facility status details report index
    public function statusDetailsIndex()
    {
        $facilities = DB::table('hospital_details_history')
            ->leftJoin('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
            ->leftJoin('lst_status', 'hospital_details_history.status_id', '=', 'lst_status.id')
            ->select(
                'hospital_details_history.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_facility_types.name as facility_type',
                'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'lst_status.status as status',
            )

            ->where('hospital_details_history.state_id', 'like', '%' .  Auth::user()->state_id . '%')
            ->where('hospital_details_history.status_id', '0')

            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('hospital_details_history.facility_name')

            ->paginate(15);

        $message = $facilities->total() . " facilities has never been updated";

        return view('reports.facility_status_details', compact('facilities', 'message'));
    }



        /**
     * Filter Facility Status Details Report
     *
     * Filters facility status details by state, LGA, and status.
     * Status codes are mapped to multiple related status IDs:
     * - Status 2 → [2, 9, 16] (Requested states)
     * - Status 3 → [3, 10, 17] (Verified states)
     * - Status 4 → [4, 11, 18] (Validated states)
     * - Status 5 → [5, 12, 19] (Published/Updated states)
     * - Status 7 → [7, 14, 21] (Rejected states)
     * - Other status codes are used directly
     *
     * @authenticated
     *
     * @bodyParam state_id integer required State ID. Example: 1
     * @bodyParam lga_id integer LGA ID. Use 0 to skip LGA filter. Example: 10
     * @bodyParam status_id integer required Status ID. Example: 2
     *
     * @response 200 scenario="Filtered Results" {
     *   "facilities": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 123,
     *         "unique_id": "FCT-001",
     *         "facility_name": "General Hospital Abuja",
     *         "state": "FCT",
     *         "lga": "Abuja Municipal",
     *         "ward": "Garki",
     *         "ownership": "Public",
     *         "facility_level": "Secondary",
     *         "operational_status": "Operational",
     *         "registration_status": "Registered",
     *         "license_status": "Licensed",
     *         "status": "New Facility Requested",
     *         "action": "Create",
     *         "created_at": "2025-01-15 10:30:00"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 23
     *   },
     *   "message": "23 facilities found"
     * }
     */
    public function statusDetailsReport(Request $request)
    {
        if ($request->status_id == 2) {
            $status = [2, 9, 16];
        } elseif ($request->status_id == 3) {
            $status = [3, 10, 17];
        } elseif ($request->status_id == 4) {
            $status = [4, 11, 18];
        } elseif ($request->status_id == 5) {
            $status = [5, 12, 19];
        } elseif ($request->status_id == 7) {
            $status = [7, 14, 21];
        } else {
            $status = [$request->status_id];
        }

        $facilities = DB::table('hospital_details_history')
            ->leftJoin('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
            ->leftJoin('lst_status', 'hospital_details_history.status_id', '=', 'lst_status.id')
            ->select(
                'hospital_details_history.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_facility_types.name as facility_type',
                'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'lst_status.status as status',
            )

            ->where('hospital_details_history.state_id', 'like', '%' .  $request->state_id . '%')
            ->where('hospital_details_history.lga_id', 'like', '%' .  $request->lga_id . '%')
            ->whereIn('hospital_details_history.status_id', $status)

            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')

            ->orderBy('facility_name')
            ->paginate(15)
            ->appends($request->all());

        $message = $facilities->total() . " facilities found";

        $request->flash('request', $request);

        return view('reports.facility_status_details', compact('facilities', 'message'));
    }



     /**
     * Download Facility Status Details Report
     *
     * Downloads the filtered facility status details as an Excel file.
     * Status mapping is the same as statusDetailsReport endpoint.
     *
     * @authenticated
     *
     * @queryParam state integer required State ID. Example: 1
     * @queryParam lga integer LGA ID. Use 0 to include all LGAs. Example: 10
     * @queryParam status integer required Status ID. Example: 2
     *
     * @response 200 scenario="Success" {
     *   "content-type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *   "filename": "data.xlsx",
     *   "description": "Excel file with columns: state, lga, ward, id, code, facility_name, ownership, level, status, action_type"
     * }
     */
    public function statusDetailsDownload(Request $request)
    {
        if ($request->status == 2) {
            $status = [2, 9, 16];
        } elseif ($request->status == 3) {
            $status = [3, 10, 17];
        } elseif ($request->status == 4) {
            $status = [4, 11, 18];
        } elseif ($request->status == 5) {
            $status = [5, 12, 19];
        } elseif ($request->status == 7) {
            $status = [7, 14, 21];
        } else {
            $status = [$request->status];
        }

        $facilities = DB::table('hospital_details_history')
            ->leftJoin('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
            ->leftJoin('lst_status', 'hospital_details_history.status_id', '=', 'lst_status.id')
            ->select(
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',

                'hospital_details_history.id as id',
                'hospital_details_history.unique_id as unique_id',
                'hospital_details_history.facility_name as facility_name',
                'lst_ownerships.name as ownership',
                'lst_level_of_care.name as facility_level',
                'lst_status.status as status',
                'hospital_details_history.action as action',

            )
            ->where('hospital_details_history.state_id', 'like', '%' .  $request->state . '%')
            ->where('hospital_details_history.lga_id', 'like', '%' .  $request->lga . '%')
            ->whereIn('hospital_details_history.status_id', $status)
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('hospital_details_history.facility_name')
            ->get();

        $column_header = array('state', 'lga', 'ward', 'id', 'code', 'facility_name', 'ownership', 'level', 'status', 'action_type');


        return Excel::download(new HFExport($facilities, $column_header), "data.xlsx");
    }
}
