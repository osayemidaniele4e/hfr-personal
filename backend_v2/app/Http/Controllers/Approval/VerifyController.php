<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\StatusTracking;
use Auth;
use Carbon\Carbon;
use App\Models\Hospital;
use App\Models\HospitalServiceHistory;
use App\Models\audit;
use App\Models\ApprovalNotifications;
use Illuminate\Support\Facades\Log;


/**
 * @group Facility Approval Tracking - Facility Verification
 *
 * This controller handles all verification-related workflows for health facility approval.
 * It allows authorized users to view pending verification requests, approve or reject facility requests,
 * recall verifications, and track verification actions.
 *
 * All actions are logged and tied to user permissions (State and LGA level).
 */
class VerifyController extends Controller
{

     /**
     * Display all pending verification requests.
     *
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 123,
     *       "state": "Lagos",
     *       "lga": "Ikeja",
     *       "ward": "Ward 1",
     *       "facility_type_name": "General Hospital",
     *       "facility_level_name": "Secondary",
     *       "ownership": "Government",
     *       "requested_by_firstname": "John",
     *       "requested_by_lastname": "Doe",
     *       "requested_email": "john@example.com",
     *       "requested_mobile": "08012345678",
     *       "status_id": 1,
     *       "updated_at": "2025-11-13 17:00:00"
     *     }
     *   ]
     * }
     */
    public function index()
    {

        // if (auth()->user()->hasAnyPermission(['lga_1000'])) {
        //     $pending = DB::table('hs_hospitals_history')
        //         ->where('state_id', '=', Auth::user()->state_id)
        //         ->whereIn('status_id', [1, 8, 15, 5, 12, 19])
        //         ->orderby('updated_at', 'desc')
        //         ->get();
        // } else {
        //     $allowedLgas = auth()->user()->getDirectPermissions()->pluck('id')->toArray();

        //     $pending = DB::table('hs_hospitals_history')
        //         ->where('state_id', Auth::user()->state_id)
        //         ->whereIn('lga_id', $allowedLgas)
        //         ->whereIn('status_id', [1, 8, 15, 5, 12, 19])
        //         ->orderBy('updated_at', 'desc')
        //         ->get();
        // }

        if (auth()->user()->hasAnyPermission(['lga_1000'])) {
            // Log::debug('User has permission for all states', [
            //     'user_id' => Auth::user()->id,
            //     'state_id' => Auth::user()->state_id,
            // ]);
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->where('hospital_details_history.state_id', Auth::user()->state_id)
                ->whereIn('hospital_details_history.status_id', [1, 8, 15, 5, 12, 19])
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',

                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level_name',
                    'lst_ownerships.name as ownership',

                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->orderBy('hospital_details_history.updated_at', 'desc')
                ->get();
        } else {
            Log::debug('User has limited permissions', [
                'user_id' => Auth::user()->id,
                'state_id' => Auth::user()->state_id,
            ]);
            $allowedLgas = auth()->user()->getDirectPermissions()->pluck('id')->toArray();

            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                // ->where('hospital_details_history.state_id', Auth::user()->state_id)
                // ->whereIn('hospital_details_history.lga_id', $allowedLgas)
                ->whereIn('hospital_details_history.status_id', [1, 8, 15, 5, 12, 19])
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',

                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level_name',
                    'lst_ownerships.name as ownership',

                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->orderBy('hospital_details_history.updated_at', 'desc')
                ->get();
        }


        // Log::debug('Pending verification requests data', [
        //     'pending_requests' => $pending->toArray(),
        // ]);

        return view('approvals.pending_verify', compact('pending'));
    }



     /**
     * Search verification requests by action and status.
     *
     *
     * @bodyParam action string required The action keyword to search for. Example: "CREATE FACILITY"
     * @bodyParam status integer required The verification status to filter by. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 124,
     *       "state": "Lagos",
     *       "lga": "Ikeja",
     *       "ward": "Ward 2",
     *       "facility_type_name": "Primary Health Centre",
     *       "facility_level_name": "Primary",
     *       "ownership": "Private",
     *       "requested_by_firstname": "Jane",
     *       "requested_by_lastname": "Smith",
     *       "requested_email": "jane@example.com",
     *       "requested_mobile": "08087654321",
     *       "status_id": 8,
     *       "updated_at": "2025-11-13 16:30:00"
     *     }
     *   ]
     * }
     */
    public function search(Request $request)
    {
        // Log::debug('Search request received', [
        //     'request' => $request->all(),
        // ]);
        $request->validate([
            'action' => 'required|string',
            'status' => 'required|integer',
        ]);
        if ($request->status == 1) {
            if (auth()->user()->hasAnyPermission(['lga_1000'])) {
                $pending = DB::table('hospital_details_history')
                    ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                    ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                    ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                    ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                    ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                    ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                    ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                    ->select(
                        'hospital_details_history.*',
                        'ou_states.name as state',
                        'ou_lgas.name as lga',
                        'ou_wards.name as ward',

                        'lst_facility_types.name as facility_type_name',
                        'lst_level_of_care.name as facility_level_name',
                        'lst_ownerships.name as ownership',

                        'users.lastname as requested_by_lastname',
                        'users.firstname as requested_by_firstname',
                        'users.email as requested_email',
                        'users.mobile as requested_mobile'
                    )
                    ->where('hospital_details_history.state_id', '=', Auth::user()->state_id)
                    ->where('hospital_details_history.action', 'like', '%' .  $request->action . '%')
                    ->whereIn('hospital_details_history.status_id', [1, 8, 15, 5, 12, 19])
                    ->orderby('hospital_details_history.updated_at', 'desc')
                    ->get();
            } else {
                Log::debug('User has limited permissions', [
                    'user_id' => Auth::user()->id,
                    'state_id' => Auth::user()->state_id,
                    'action' => $request->action
                ]);
                $pending = DB::table('hospital_details_history')
                    ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                    ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                    ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                    ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                    ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                    ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                    ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                    ->select(
                        'hospital_details_history.*',
                        'ou_states.name as state',
                        'ou_lgas.name as lga',
                        'ou_wards.name as ward',
                        'lst_facility_types.name as facility_type_name',
                        'lst_level_of_care.name as facility_level_name',
                        'lst_ownerships.name as ownership',
                        'users.lastname as requested_by_lastname',
                        'users.firstname as requested_by_firstname',
                        'users.email as requested_email',
                        'users.mobile as requested_mobile'
                    )
                    ->where('hospital_details_history.state_id', Auth::user()->state_id)
                    ->when($request->filled('action'), function ($query) use ($request) {
                        $query->where('hospital_details_history.action', 'like', '%' . $request->action . '%');
                    })
                    ->when(auth()->user()->getDirectPermissions()->isNotEmpty(), function ($query) {
                        $query->whereIn('hospital_details_history.lga_id', auth()->user()->getDirectPermissions()->pluck('id')->toArray());
                    })
                    ->whereIn('hospital_details_history.status_id', [1, 8, 15, 5, 12, 19])
                    ->orderBy('hospital_details_history.updated_at', 'desc')
                    ->get();
            }
        } elseif ($request->status == 2) {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level_name',
                    'lst_ownerships.name as ownership',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.verified_id', Auth::user()->id)
                ->when($request->filled('action'), function ($query) use ($request) {
                    $query->where('hospital_details_history.action', 'like', '%' . $request->action . '%');
                })
                ->whereIn('hospital_details_history.status_id', [2, 9, 16])
                ->orderBy('hospital_details_history.updated_at', 'desc')
                ->get();
        } elseif ($request->status == 3) {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level_name',
                    'lst_ownerships.name as ownership',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.verified_id', Auth::user()->id)
                ->when($request->filled('action'), function ($query) use ($request) {
                    $query->where('hospital_details_history.action', 'like', '%' . $request->action . '%');
                })
                ->whereIn('hospital_details_history.status_id', [3, 10, 17])
                ->orderBy('hospital_details_history.updated_at', 'desc')
                ->get();
        } else {
            $pending = DB::table('hospital_details_history')
                ->join('ou_states', 'hospital_details_history.state_id', '=', 'ou_states.id')
                ->join('ou_lgas', 'hospital_details_history.lga_id', '=', 'ou_lgas.id')
                ->join('users', 'hospital_details_history.requested_by', '=', 'users.id')
                ->join('ou_wards', 'hospital_details_history.ward_id', '=', 'ou_wards.id')
                ->join('lst_facility_types', 'hospital_details_history.facility_type_id', '=', 'lst_facility_types.id')
                ->join('lst_level_of_care', 'hospital_details_history.facility_level_id', '=', 'lst_level_of_care.id')
                ->join('lst_ownerships', 'hospital_details_history.ownership_id', '=', 'lst_ownerships.id')
                ->select(
                    'hospital_details_history.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level_name',
                    'lst_ownerships.name as ownership',
                    'users.lastname as requested_by_lastname',
                    'users.firstname as requested_by_firstname',
                    'users.email as requested_email',
                    'users.mobile as requested_mobile'
                )
                ->where('hospital_details_history.verified_id', '=', Auth::user()->id)
                ->when($request->filled('action'), function ($query) use ($request) {
                    $query->where('hospital_details_history.action', 'like', '%' . $request->action . '%');
                })
                ->orderBy('hospital_details_history.updated_at', 'desc')
                ->get();
        }

        $request->flash('request', $request);
        return view('approvals.pending_verify', compact('pending'));
    }


     /**
     * Approve or reject a verification request.
     *
     *
     * @bodyParam id integer required The ID of the hospital request. Example: 123
     * @bodyParam action string required The action to take: "approve" or "reject". Example: "approve"
     * @bodyParam requested_action string required The type of request: "CREATE FACILITY", "UPDATE FACILITY", "DELETE FACILITY". Example: "CREATE FACILITY"
     * @bodyParam notes string optional Notes for verification. Example: "Checked all details."
     * @bodyParam validated_by integer optional The user ID of the validator.
     * @bodyParam validated_at string optional The timestamp of validation. Format: Y-m-d H:i:s
     * @bodyParam published_by integer optional The user ID of the publisher.
     * @bodyParam published_at string optional The timestamp of publishing. Format: Y-m-d H:i:s
     * @bodyParam publish_note string optional Notes for publishing.
     *
     * @response 200 {
     *   "message": "Facility Creation Verified",
     *   "hospital_id": 123,
     *   "status_id": 2
     * }
     *
     * @response 500 {
     *   "error": "Exception message"
     * }
     */
    public function store(Request $request)
    {

        Hospital::disableAuditing();
        $hosp = new Hospital;
        $hosp = Hospital::findOrFail($request->id);
        $user = Auth::user();

        if ($request->action == "approve") {
            if ($request->requested_action == "CREATE FACILITY") {
                $status_id = 2;
                $action = "Create Verified";
                $message = "Facility Creation Verified";
                $mail_message = "Facility creation request has been verified. Please login to the system to review and validate the request.";
            } elseif ($request->requested_action == "UPDATE FACILITY") {
                $status_id = 9;
                $action = "Update Verified";
                $message = "Facility Update Verified";
                $mail_message = "Facility update request has been verified. Please login to the system to review and validate the request.";
            } else {
                $status_id = 16;
                $action = "Delete Verified";
                $message = "Facility Deletion Verified";
                $mail_message = "Facility deletion request has been verified. Please login to the system to review and validate the request.";
            }
            //clear publish and validate fields after reqest rejected then re submiited
            $hosp->validated_by =  $request->validated_by;
            $hosp->validated_at =  $request->validated_at;
            $hosp->validate_note = $request->validate_note;
            $hosp->published_by = $request->published_by;
            $hosp->published_at = $request->published_at;
            $hosp->publish_note = $request->publish_note;
        }

        if ($request->action == "reject") {
            if ($request->requested_action == "CREATE FACILITY") {
                $status_id = 3;
                $action = "Create Verification Rejected";
                $message = "Facility Creation Rejected";
                $mail_message = "Verifier has rejected facility creation request. Please login to the system to review your request.";
            } elseif ($request->requested_action == "UPDATE FACILITY") {
                $status_id = 10;
                $action = "Update Verification Rejected";
                $message = "Facility Update Rejected";
                $mail_message = "Verifier has rejected facility update request. Please login to the system to review your request.";
            } else {
                $status_id = 17;
                $action = "Delete Verification Rejected";
                $message = "Facility Deletion Rejected";
                $mail_message = "Verifier has rejected facility deletion request. Please login to the system to review your request.";
            }
        }

        $date = Carbon::now()->format('Y-m-d H:i:s');

        $hosp->status_id = $status_id;

        $hosp->verified_by = $user->id;
        $hosp->verified_id = $user->id;
        $hosp->verified_email = $user->email;
        $hosp->verified_mobile = $user->mobile;

        $hosp->verified_at = $date;
        $hosp->verify_note = $request->notes;

        $status = new StatusTracking;
        $status->hospital_id = $request->id;
        $status->user_id = $user->id;
        $status->status_id = $status_id;
        $status->note = $request->notes;
        $status->created_at =  $date;

        DB::beginTransaction();
        try {
            $hosp->save();
            $status->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        Hospital::enableAuditing();

        //send notifications
        if (config('hfr.notify_validator')) {
            $notify = new ApprovalNotifications;
            $notify->sendVerificationNotification($mail_message, $request->action, $hosp->requested_by);
        }


        session()->flash("alert-success", $message);


        if ($status_id == 9 or $status_id == 10) {
            return redirect()->route('verify.pending');
        } else {
            return redirect()->back();
        }
    }


    /**
     * Recall a previously verified request.
     *
     *
     * @bodyParam hosp_id integer required The ID of the hospital request to recall. Example: 123
     * @bodyParam action string required The action type of the request: "CREATE FACILITY", "UPDATE FACILITY", "DELETE FACILITY". Example: "CREATE FACILITY"
     * @bodyParam verified_by integer optional The user ID of the verifier.
     * @bodyParam verified_at string optional The timestamp of verification. Format: Y-m-d H:i:s
     * @bodyParam verified_note string optional Notes for recall.
     *
     * @response 200 {
     *   "message": "Verification recalled successfully!",
     *   "hospital_id": 123,
     *   "status_id": 1
     * }
     *
     * @response 400 {
     *   "message": "Can not recall validated or published request!"
     * }
     */
    public function recall(Request $request)
    {
        if ($this->isVerified($request->hosp_id)) {
            if ($request->action == "CREATE FACILITY") {
                $status_id = 1;
                $action = "Recall Create Verification";
            } elseif ($request->action == "UPDATE FACILITY") {
                $status_id = 8;
                $action = "Recall Update Verification";
            } else {
                $status_id = 15;
                $action = "Recall Delete Verification";
            }


            $date = Carbon::now()->format('Y-m-d H:i:s');

            Hospital::disableAuditing();
            $hosp = new Hospital;
            $hosp = Hospital::findOrFail($request->hosp_id);
            $hosp->status_id = $status_id;
            $hosp->verified_by = $request->verified_by;
            $hosp->verified_at = $request->verified_at;
            $hosp->verify_note = $request->verified_note;

            $status = new StatusTracking;
            $status->hospital_id = $request->hosp_id;
            $status->user_id = Auth::user()->id;
            $status->status_id = $status_id;
            $status->created_at =  $date;
            $status->note = $action;

            DB::beginTransaction();
            try {
                $hosp->save();
                $status->save();

                DB::commit();
            } catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['error' => $ex->getMessage()], 500);
            }

            Hospital::enableAuditing();

            session()->flash("alert-success", "Verification recalled successfully!");
        } else {
            session()->flash("alert-success", "Can not recall validated or published request!");
        }

        return redirect()->route('verify.pending');
    }

    private function isVerified($id)
    {
        $hosp = Hospital::find($id);

        if (in_array($hosp->status_id, [2, 9, 16])) {
            return true;
        } else {
            return false;
        }
    }
}
