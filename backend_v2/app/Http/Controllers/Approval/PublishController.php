<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\StatusTracking;
use Auth;
use Carbon\Carbon;
use App\Models\Hospital;
use App\Models\HospitalService;
use App\Models\HospitalServiceHistory;
use App\Models\audit;
use App\Models\ApprovalNotifications;
use App\Models\HfrDhis;



/**
 * @group Facility Approval Tracking - Publication
 *
 * Endpoints for managing the publication process of health facility data.
 * 
 * These endpoints handle reviewing, approving, rejecting, and finalizing
 * facility publication requests, as well as syncing with DHIS2 where enabled.
 *
 * @subgroup Facility Publication Requests
 * This subgroup contains endpoints that manage pending, approved, and rejected publication requests.
 */
class PublishController extends Controller
{

     /**
     * Display the list of facilities pending publication.
     *
     * Retrieves all facilities whose publication requests are awaiting approval.
     * 
     * 
     * @response 200 scenario=success View showing list of pending facilities ready for publication.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pending = DB::table('hospital_details_history')
            ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
            ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
            ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_ownership_types', 'hospital_details_history.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftjoin('lst_level_of_care_options', 'hospital_details_history.facility_level_option_id', '=', 'lst_level_of_care_options.id')
            ->join('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftjoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftjoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
            ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
            ->select(
                'hospital_details_history.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
                'lst_ownership_types.type as ownership_type',
                'lst_level_of_care.name as facility_level',
                'lst_level_of_care_options.description as facility_level_option',
                'lst_oparational_status.status as operation_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'users.lastname as requested_by_lastname',
                'users.firstname as requested_by_firstname',
                'users.email as requested_email',
                'users.mobile as requested_mobile'
            )
            ->where('hospital_details_history.state_id', 'like', '%' .  Auth::user()->state_id . '%')
            ->whereIn('hospital_details_history.status_id', [4, 11, 18])
            ->get();


        return view('approvals.pending_publish', compact('pending'));
    }


      /**
     * Search and filter facilities based on their publication status.
     *
     * Allows filtering by state, action type, and publication status:
     *  - 1 → Pending publication
     *  - 2 → Approved/published
     *  - 3 → Rejected publication
     * 
     *
     * @queryParam status int required The publication status ID (1=pending, 2=published, 3=rejected).
     * @queryParam state_id int optional The ID of the state to filter by.
     * @queryParam action string optional The action type (e.g. "approve", "reject").
     * 
     * @response 200 scenario=success View showing filtered list of facilities.
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->status == 1) {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->leftjoin('lst_ownership_types', 'hospital_details_history.ownership_type_id', '=', 'lst_ownership_types.id')
                ->leftjoin('lst_level_of_care_options', 'hospital_details_history.facility_level_option_id', '=', 'lst_level_of_care_options.id')
                ->join('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
                ->leftjoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
                ->leftjoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_ownerships.name as ownership',
                    'lst_ownership_types.type as ownership_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_level_of_care_options.description as facility_level_option',
                    'lst_oparational_status.status as operation_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.state_id', 'like', '%' .  $request->state_id . '%')
                ->where('hospital_details_history.action', 'like', '%' .  $request->action . '%')
                ->whereIn('hospital_details_history.status_id', [4, 11, 18])
                ->get();
        } elseif ($request->status == 2) {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->join('lst_ownership_types', 'hospital_details_history.ownership_type_id', '=', 'lst_ownership_types.id')
                ->leftjoin('lst_level_of_care_options', 'hospital_details_history.facility_level_option_id', '=', 'lst_level_of_care_options.id')
                ->join('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
                ->leftjoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
                ->leftjoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_ownerships.name as ownership',
                    'lst_ownership_types.type as ownership_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_level_of_care_options.description as facility_level_option',
                    'lst_oparational_status.status as operation_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.published_by', '=', Auth::user()->id)
                ->where('hospital_details_history.state_id', 'like', '%' .  $request->state_id . '%')
                ->where('hospital_details_history.action', 'like', '%' .  $request->action . '%')
                ->whereIn('hospital_details_history.status_id', [6, 13, 20])
                ->get();
        } elseif ($request->status == 3) {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->join('lst_ownership_types', 'hospital_details_history.ownership_type_id', '=', 'lst_ownership_types.id')
                ->leftjoin('lst_level_of_care_options', 'hospital_details_history.facility_level_option_id', '=', 'lst_level_of_care_options.id')
                ->join('lst_oparational_status', 'hospital_details_history.operational_status_id', '=', 'lst_oparational_status.id')
                ->leftjoin('lst_registration_status', 'hospital_details_history.registration_status_id', '=', 'lst_registration_status.id')
                ->leftjoin('lst_license_status', 'hospital_details_history.license_status_id', '=', 'lst_license_status.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_ownerships.name as ownership',
                    'lst_ownership_types.type as ownership_type',
                    'lst_level_of_care.name as facility_level',
                    'lst_level_of_care_options.description as facility_level_option',
                    'lst_oparational_status.status as operation_status',
                    'lst_registration_status.status as registration_status',
                    'lst_license_status.status as license_status',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.published_by', '=', Auth::user()->id)
                ->where('hospital_details_history.state_id', 'like', '%' .  $request->state_id . '%')
                ->where('hospital_details_history.action', 'like', '%' .  $request->action . '%')
                ->whereIn('hospital_details_history.status_id', [7, 14, 21])
                ->get();
        }

        $request->flash('request', $request);
        return view('approvals.pending_publish', compact('pending'));
    }


      /**
     * Approve or reject a facility publication request.
     *
     * Publishes or rejects facility creation, update, or deletion requests.
     * When integration is enabled, synchronizes facility data with DHIS2.
     * 
     *
     * @bodyParam id int required The facility history ID being processed.
     * @bodyParam action string required Either "approve" or "reject".
     * @bodyParam requested_action string required The original request type ("CREATE FACILITY", "UPDATE FACILITY", "DELETE FACILITY").
     * @bodyParam notes string optional Notes or comments for the approval or rejection.
     *
     * @response 200 scenario=success Facility publication processed successfully.
     * @response 500 scenario=error Internal error during publication process.
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check if the request is publised
        if (!$this->isPublished($request->id)) {
            Hospital::disableAuditing();
            $hosp = new Hospital();
            $hosp = Hospital::find($request->id);
            $facility_name = $hosp['facility_name'];
            $state_id = $hosp['state_id'];
            $ward_id = $hosp['ward_id'];
            $mail_subject = "";
            $user = Auth::user();

            if ($request->action == "approve") {
                if ($request->requested_action == "CREATE FACILITY") {
                    $status_id = 6;
                    $message = "Facility Published";
                    $action = "Create Published";
                    $mail_subject = "New Facility Created";
                    $mail_message = "New facility: '" . $facility_name . "' have been created";
                } elseif ($request->requested_action == "UPDATE FACILITY") {
                    $status_id = 13;
                    $message = "Facility Update Published";
                    $action = "Update Published";
                    $mail_subject = "Facility Updated";
                    $mail_message = "Facility: '" . $facility_name . "' have been updated.";
                } else {
                    $status_id = 20;
                    $message = "Facility Deleted";
                    $action = "Delete Published";
                    $mail_subject = "Facility Deleted";
                    $mail_message = "Facility: '" . $facility_name . "' have been deleted.";
                }
            }

            if ($request->action == "reject") {
                if ($request->requested_action == "CREATE FACILITY") {
                    $status_id = 7;
                    $message = "Facility Publish Rejected";
                    $action = "Create Publish Rejected";
                    $mail_message = "Publisher has rejected facility creation request. Please login to the system to review your request.";
                } elseif ($request->requested_action == "UPDATE FACILITY") {
                    $status_id = 14;
                    $message = "Facility Publish Rejected";
                    $action = "Update Publish Rejected";
                    $mail_message = "Publishere has rejected facility update request. Please login to the system to review your request.";
                } else {
                    $status_id = 21;
                    $message = "Facility Publish Rejected";
                    $action = "Delete Publish Rejected";
                    $mail_message = "Publisher has rejected facility deletion request. Please login to the system to review your request.";
                }
            }

            DB::beginTransaction();
            try {

                $date = Carbon::now()->format('Y-m-d H:i:s');

                $hosp->status_id = $status_id;

                $hosp->published_by =  $user->id;
                $hosp->published_email = $user->email;
                $hosp->published_mobile = $user->mobile;


                $hosp->published_at = $date;
                $hosp->publish_note = $request->notes;
                $hosp->save();
                Hospital::enableAuditing();

                $status = new StatusTracking;
                $status->hospital_id = $request->id;
                $status->user_id =  $user->id;
                $status->status_id = $status_id;
                $status->note = $request->notes;
                $status->created_at = $date;
                $status->save();

                //insert new facility data to main table after published
                if ($status_id == 6) {
                    $hosp_history = new Hospital;
                    $hosp_history = Hospital::find($request->id);

                    //copy data from  history to main
                    $hosp_main = new Hospital;
                    $hosp_main->fill($hosp_history->toArray());
                    $hosp_main->unique_id = $hosp_history->unique_id;
                    $hosp_main->start_date = $hosp_history->start_date;
                    $hosp_main->status_id = $hosp_history->status_id;
                    $hosp_main->created_by = $hosp_history->created_by;
                    $hosp_main->operational_days =  $hosp_history->operational_days;
                    $hosp_main->save();
                    //copy ends

                    //get new hospital services
                    $services = DB::select("SELECT service_id FROM hs_hospital_services_history WHERE hospital_id = " . $request->id . "");

                    //insert services
                    if (!empty($services)) {
                        foreach ($services as $service) {
                            $hosp_services = new HospitalService;
                            $hosp_services->service_id = $service->service_id;
                            $hosp_services->hospital_id = $request->id;
                            $hosp_services->save();
                        }
                    }
                }

                //update hospital, and hospital services to main table
                if ($status_id == 13) {
                    $hosp_history = new Hospital;
                    $hosp_history = Hospital::find($request->id);

                    //copy data from  history to main
                    $hosp_main = new Hospital;
                    $hosp_main = Hospital::find($request->id);
                    $hosp_main->fill($hosp_history->toArray());
                    $hosp_main->start_date = $hosp_history->start_date;
                    $hosp_main->status_id = $hosp_history->status_id;
                    $hosp_main->created_by = $hosp_history->created_by;
                    $hosp_main->operational_days =  $hosp_history->operational_days;
                    $hosp_main->save();
                    //copy ends

                    //get new hospital services
                    $services = DB::select("SELECT service_id FROM hs_hospital_services_history WHERE hospital_id = " . $request->id . "");

                    if (!empty($services)) {
                        // remove current services in main table
                        $deleted = DB::delete("delete from hs_hospital_services where hospital_id ='" . $request->id . "' and id > 0");

                        //add new services
                        foreach ($services as $service) {
                            $hosp_services = new HospitalService;
                            $hosp_services->service_id = $service->service_id;
                            $hosp_services->hospital_id = $request->id;
                            $hosp_services->save();
                        }
                    }
                }

                //Delete facility after final delete request published
                if ($status_id == 20) {
                    HospitalService::where('hospital_id', $request->id)->delete();
                    Hospital::destroy($request->id);
                }

                DB::commit();
            } catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['error' => $ex->getMessage()], 500);
            }

            //****** Send Notifications *********
            if (config('hfr.notify_publication')) {
                $notify = new ApprovalNotifications;
                $notify->sendPublicationNotification($mail_message, $request->action, $mail_subject, $state_id);
            }

            session()->flash("alert-success", $message);

            if (config('hfr.integration_enabled')) {

                //******************************************************************************************************
                // HFR DHIS 2 EXCHANGE
                //******************************************************************************************************
                //after publishing new facility, create a facility in dhis2 and send notifcation
                $dhis = new HfrDhis;

                if ($status_id == 6) {
                    return view('dhis.store', compact('hosp', 'message'));
                } elseif ($status_id == 13) {  //after publishing facility updates, send updates to dhis2 and send notifcation
                    $data = $dhis->getDhisUpdatedValues($hosp, $request->id);
                    $id = $request->id;

                    if ($data != false) {
                        return view('dhis.update', compact('data', 'id', 'message'));
                    } else {
                        return redirect()->route('publish.pending');
                    }
                } else {
                    //after publishing delete request,  delete facility in DHIS if it has no data or close if it has data

                    return view('dhis.delete', compact('hosp', 'message'));

                    // $dhis->sendEmailtoDhisTeamForDeletedFacility($facility_name, $ward_id);
                    // return redirect()->route('publish.pending');
                }

                //******************************************************************************************************
                // HFR DHIS 2 EXCHANGE END..
                //******************************************************************************************************
            } else {

                if ($status_id == 13) {
                    return redirect()->route('publish.pending');
                } else {
                    return redirect()->back();
                }
            }
            //facility is already published
        } else {
            session()->flash("alert-success", 'The request is already published');
            return redirect()->back();
        }
    }


    /**
     * Check if a facility has already been published.
     *
     * Prevents re-processing of already published requests by checking
     * the status ID in the facility’s history record.
     * 
     * 
     * @param int $fac_id The facility ID.
     * @return bool True if published, otherwise false.
     */
    //this method check to see if the request is arleady published
    //before trying to publish
    public function isPublished($fac_id)
    {
        $hosp = new Hospital();
        $hosp = Hospital::find($fac_id);

        if ($hosp['status_id'] == 6 or $hosp['status_id'] == 13 or $hosp['status_id'] == 20) {
            return true;
        } else {
            return false;
        }
    }
}
