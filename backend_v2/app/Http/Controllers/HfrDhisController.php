<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Models\DhisLog;
use App\Models\HfrDhis;
use App\Models\DhisLookup;
use Auth;
use Carbon\Carbon;
use App\Models\Hospital;
use Illuminate\Support\Facades\Log;



/**
 * @group Administration - DHIS2 Synchronization
 *
 * APIs for synchronizing healthcare facility data with DHIS2 (District Health Information System).
 * Handles creation, updates, and deletion of organization units in DHIS2, including assignment 
 * of ownership and level of care groups. All operations are logged for audit purposes.
 */
class HfrDhisController extends Controller
{



     /**
     * Create Facility in DHIS2
     *
     * Creates a new organization unit (facility) in DHIS2 with all associated metadata including
     * ownership, level of care, and optional level of care options. Automatically handles facility
     * closure date based on operational status. Sends email notification to DHIS2 team upon success.
     * All operations are logged to dhis_log table for audit trail.
     *
     * @authenticated
     *
     * @bodyParam id integer required HFR facility ID (used as DHIS2 code). Example: 12345
     * @bodyParam facility_name string required Facility name. Example: General Hospital Abuja
     * @bodyParam alt_facility_name string Alternative/short facility name. Example: Gen Hosp Abuja
     * @bodyParam state_id integer required State ID for parent organization unit lookup. Example: 1
     * @bodyParam ward_id integer required Ward ID for parent organization unit assignment. Example: 50
     * @bodyParam operational_status_id integer required Operational status: 1 (Operational), 2-4 (Temporarily closed - auto-sets close date), 5+ (Permanently closed - uses provided close date). Example: 1
     * @bodyParam ownership_id integer required Ownership type ID for group assignment. Example: 1
     * @bodyParam facility_level_id integer required Facility level/care level ID for group assignment. Example: 3
     * @bodyParam facility_level_option_id integer Facility level option ID. Use 0 to skip. Example: 0
     * @bodyParam start_date date Facility opening date. Example: 2020-01-15
     * @bodyParam close_date date Facility closure date (required if operational_status_id > 4). Example: 2024-12-31
     * @bodyParam postal_address string Postal address. Example: P.O. Box 123, Abuja
     * @bodyParam longitude string Longitude coordinate. Example: 7.3986
     * @bodyParam latitude string Latitude coordinate. Example: 9.0765
     * @bodyParam email_address string Email address. Example: info@hospital.com
     * @bodyParam website string Website URL. Example: https://hospital.com
     * @bodyParam phone_number string Phone number. Example: +234-123-456-7890
     *
     * @response 200 scenario="Success" "Created"
     *
     * @response 500 scenario="DHIS2 API Error" "Exception_Error"
     *
     * @response 500 scenario="No Server Response" "Exception_Error"
     *
     * @apiResourceAdditional Returns string "Created" on success or "Exception_Error" on failure. Check dhis_log table for detailed status including ownership_status, level_status, and level_option_status.
     */
    public function store(Request $request)
    {
        $dhis = new HfrDhis;

        $data = [];

        if ($request->operational_status_id > 1 && $request->operational_status_id < 5) {
            $close_date = Carbon::now()->format('Y-m-d');
        } elseif ($request->operational_status_id > 4) {
            $close_date = $request->close_date;
        } else {
            $close_date = '';
        }

        $data = [
            'name' => $dhis->formatName($request->facility_name, $request->state_id),
            'shortName' => $dhis->getShortname($request->facility_name, $request->alt_facility_name),
            'code' => $request->id,
            'openingDate' => $dhis->formatDate($request->start_date),
            'closedDate' => $dhis->formatDate($close_date),
            'address' => $request->postal_address,
            'coordinates' => $dhis->formatGeoCords($request->longitude, $request->latitude),
            'email' => $request->email_address,
            'url' => $request->website,
            'phoneNumber' => $request->phone_number,
            'parent' => $dhis->getParent($request->ward_id)
        ];

        try {
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response = $client->post('organisationUnits', [
                'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                'json' => $data
            ]);

            
           // Log::info('response from request'.$response->getBody());
          //  Log::info('response phrase: '.$response->getReasonPhrase());
            if ($response->getReasonPhrase() ==  'Created') {
                $array = json_decode($response->getBody()->getContents(), true);

                Log::info('about to retrieve facility uid');
                $facility_uid = $array['response']['uid'];
                Log::info('facility_uid: '.$facility_uid);

                //Assign Organisation unit - ownership
                Log::info("about to assign ownership");
                $ownership_status = $dhis->assignOwnership($request->ownership_id, $facility_uid);
                  Log::info("successfully asign ownership");

                //Assign Organisation unit - Level of Care
                 Log::info("about to assign level of care");
                $level_status = $dhis->assignLevelOfCare($request->facility_level_id, $facility_uid);
                 Log::info("successfully assign level of care");

                //Assign Organisation unit - Level of Care Options
                if ($request->facility_level_option_id > 0) {
                    $level_option_status = $dhis->assignLevelOfCareOption($request->facility_level_option_id, $facility_uid);
                } else {
                    $level_option_status[0] = '';
                }


                //Save status of actions
                $error = '';
                if ($ownership_status[0] == 'Failed') {
                    $error = 'Ownership assignment error: ' . $ownership_status[1];
                }
                if ($level_status[0] == 'Failed') {
                    $error = $error . ' Level of care assignment error: ' . $level_status[1];
                }
                if ($level_option_status[0] == 'Failed') {
                    $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
                }

                $log = new DhisLog;
                $log->hfr_id = $request->id;
                $log->dhis_uid = $facility_uid;
                $log->facility_status = 'Created';
                $log->ownership_status = $ownership_status[0];
                $log->level_status = $level_status[0];
                $log->level_option_status = $level_option_status[0];
                $log->error_details = $error;
                $log->request_type = 'Create';
                $log->user_id = Auth::user()->id;
                $log->save();

                //send notification email to dhis team
                $dhis->sendEmailtoDhisTeamForNewFacility($request->facility_name, $request->ward_id);

                return 'Created';
            }
        } catch (RequestException $e) {
            $log = new DhisLog;
            if ($e->hasResponse()) {
                $log->hfr_id = $request->id;
                $log->facility_status = 'Failed';
                $log->error_details = $e->getResponse()->getBody()->getContents();
                $log->user_id = Auth::user()->id;
                $log->request_type = 'Create';
                $log->save();
                return "Exception_Error";
            } else {
                $log->hfr_id = $request->id;
                $log->facility_status = "Failed";
                $log->error_details = 'No response from the server';
                $log->user_id = Auth::user()->id;
                $log->request_type = 'Create';
                $log->save();
                return "Exception_Error";
            }
        }
    }

    public function store2()
    {
        $dhis = new HfrDhis;

        $data = [];
        $parent = [];
        $parent['id'] = 'allHBmjrOUA';

        $data = [
            'name' => 'Beatus Test Facility',
            'shortName' => 'Beatus TF',
            // 'code' => '12456',
            'openingDate' => '2019-05-05',
            'closedDate' => '',
            'address' => 'Box 777 Dar',
            'email' => 'hfr@healt.com',
            // 'url' => 'www.hfr.com',
            'phoneNumber' => '12345678',
            'parent' => $parent
        ];

        try {
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response = $client->post('organisationUnits', [
                'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                'json' => $data
            ]);


            if ($response->getReasonPhrase() ==  'Created') {
                $array = json_decode($response->getBody()->getContents(), true);

                $facility_uid = $array['response']['uid'];

                //Assign Organisation unit - ownership
                $ownership_status = $dhis->assignOwnership('1', $facility_uid);

                //Assign Organisation unit - Level of Care
                $level_status = $dhis->assignLevelOfCare('1', $facility_uid);

                //Assign Organisation unit - Level of Care Options
                if (in_array('3', [1, 2, 5])) {
                    $level_option_status = $dhis->assignLevelOfCareOption('5', $facility_uid);
                } else {
                    $level_option_status[0] = '';
                }

                //Save status of actions to log
                $error = '';
                if ($ownership_status[0] == 'Failed') {
                    $error = 'Ownership assignment error: ' . $ownership_status[1];
                }
                if ($level_status[0] == 'Failed') {
                    $error = $error . ' Level of care assignment error: ' . $level_status[1];
                }
                if ($level_option_status[0] == 'Failed') {
                    $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
                }

                $log = new DhisLog;
                $log->hfr_id = '71959570';
                $log->dhis_uid = $facility_uid;
                $log->facility_status = 'Created';
                $log->ownership_status = $ownership_status[0];
                $log->level_status = $level_status[0];
                $log->level_option_status = $level_option_status[0];
                $log->error_details = $error;
                $log->user_id = Auth::user()->id;
                $log->save();

                //send notification email to dhis team
                //$dhis->sendEmailtoDhisTeamForNewFacility($request->facility_name, $request->ward_id);

                dd('Created');
            }
        } catch (RequestException $e) {
            $log = new DhisLog;
            if ($e->hasResponse()) {
                $log->hfr_id = '71959570';
                $log->facility_status = 'Failed';
                $log->error_details = $e->getResponse()->getBody()->getContents();
                $log->user_id = Auth::user()->id;
                $log->save();
                dd("Exception_Error1");
            } else {
                $log->hfr_id = '71959570';
                $log->facility_status = "Failed";
                $log->error_details = 'No response from the server';
                $log->user_id = Auth::user()->id;
                $log->save();
                dd("Exception_Error2");
            }
        }
    }


       /**
     * Update Facility in DHIS2
     *
     * Updates an existing organization unit (facility) in DHIS2. Supports updating basic facility
     * information and reassigning organization unit groups (ownership, level of care, level options).
     * Only updates groups that are specified in the request data. Sends email notification to DHIS2
     * team upon success. All operations are logged.
     *
     * @authenticated
     *
     * @bodyParam id integer required HFR facility ID. Example: 12345
     * @bodyParam data array required Update data containing two keys:
     * - updates: Object with facility fields to update (name, shortName, address, coordinates, etc.)
     * - groups: Object with group assignments to update (ownership_id, facility_level_id, facility_level_option_id) or "empty" to skip
     * @bodyParam data.updates object Facility fields to update in DHIS2.
     * @bodyParam data.groups object|string Organization unit groups to reassign. Use "empty" to skip group updates.
     * @bodyParam data.facility_name string Facility name for email notification. Example: General Hospital Abuja
     * @bodyParam data.ward_id integer Ward ID for email notification. Example: 50
     *
     * @response 200 scenario="Success" "Updated"
     *
     * @response 500 scenario="DHIS2 API Error" "Exception_Error"
     *
     * @response 500 scenario="Facility Not Found in DHIS2" "Exception_Error"
     *
     * @response 500 scenario="Failed to Get DHIS2 UID" "Exception_Error"
     *
     * @apiResourceAdditional The endpoint automatically unassigns old groups before assigning new ones. Groups are only updated if specified in data.groups parameter.
     * @apiResourceAdditional DHIS2 UID must be 11 characters long. If not found or invalid, operation fails with detailed error logged.
     */
    public function update(Request $request)
    {
        $dhis = new HfrDhis;
        $id = $request->id;
        $data = $request->data;

        $uid = $dhis->getDhisFacilityUID($id);


        if (strlen($uid) == 11) {
            try {
                $client = new Client([
                    'base_uri' =>  config('hfr.dhis_url')
                ]);

                $response = $client->put('organisationUnits/' . $uid, [
                    'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                    'json' => $data['updates']
                ]);

                $status = $response->getReasonPhrase();
                if ($status == 'OK') {
                    $update_status = "Updated";
                } else {
                    $update_status = $status;
                }


                $ownership_status[0] = '';
                $level_status[0] = '';
                $level_option_status[0] = '';

                //if any of the organiation groups is updated
                if ($data['groups'] != 'empty') {

                    foreach ($data['groups'] as $key => $value) {
                        switch ($key) {
                            case "ownership_id":
                                $dhis->unAssignOwnership($value, $uid);
                                $ownership_status = $dhis->AssignOwnership($value, $uid);
                                break;
                            case "facility_level_id":
                                $dhis->unAssignLevelOfCare($value, $uid);
                                $level_status = $dhis->AssignLevelOfCare($value, $uid);
                                break;
                            case "facility_level_option_id":
                                if ($value > 0) {
                                    $dhis->unAssignLevelOfCareOption($value, $uid);
                                    $level_option_status = $dhis->AssignLevelOfCareOption($value, $uid);
                                } else {
                                    $level_option_status[0] = '';
                                }
                                break;
                        }
                    }
                }

                //Save status of actions to log
                $error = '';
                if ($ownership_status[0] == 'Failed') {
                    $error = 'Ownership assignment error: ' . $ownership_status[1];
                }
                if ($level_status[0] == 'Failed') {
                    $error = $error . ' Level of care assignment error: ' . $level_status[1];
                }
                if ($level_option_status[0] == 'Failed') {
                    $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
                }

                $log = new DhisLog;
                $log->hfr_id = $id;
                $log->dhis_uid = $uid;
                $log->facility_status = $update_status;
                $log->ownership_status = $ownership_status[0];
                $log->level_status = $level_status[0];
                $log->level_option_status = $level_option_status[0];
                $log->error_details = $error;
                $log->request_type = 'Update';
                $log->user_id = Auth::user()->id;
                $log->save();

                //send notification email to dhis team
                $dhis->sendEmailtoDhisTeamForUpdatedFacility($data['facility_name'], $data['ward_id']);

                return 'Updated';
            } catch (RequestException $e) {
                $log = new DhisLog;
                if ($e->hasResponse()) {
                    $log->hfr_id = $request->id;
                    $log->facility_status = 'Failed';
                    $log->error_details = $e->getResponse()->getBody()->getContents();
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Update';
                    $log->save();
                    return "Exception_Error";
                } else {
                    $log->hfr_id = $request->id;
                    $log->facility_status = "Failed";
                    $log->error_details = 'No response from the server';
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Update';
                    $log->save();
                    return "Exception_Error";
                }
            }
        } else { // if failed to get facility uid from dhis
            $log = new DhisLog;
            $log->hfr_id = $id;
            $log->facility_status = "Failed";
            $log->error_details = 'Failed to get matching facility in DHIS2';
            $log->user_id = Auth::user()->id;
            $log->save();
            return "Exception_Error";
        }
    }



  /**
     * Retry Failed DHIS2 Operations
     *
     * Attempts to retry previously failed DHIS2 operations (create, update, or group assignments).
     * Retrieves facility data from database and reattempts the failed operation based on the log entry.
     * Useful for handling temporary network issues or DHIS2 server downtime.
     *
     * @authenticated
     *
     * @bodyParam log_id integer required ID of the failed log entry to retry. Example: 456
     * @bodyParam facility_id integer required HFR facility ID. Example: 12345
     * @bodyParam facility_status string Status of facility operation: "Failed" to retry facility creation/update. Example: Failed
     * @bodyParam ownership_status string Status of ownership assignment: "Failed" to retry ownership group assignment. Example: Success
     * @bodyParam level_status string Status of level assignment: "Failed" to retry level of care group assignment. Example: Success
     * @bodyParam level_option_status string Status of level option assignment: "Failed" to retry level option group assignment. Example: Failed
     * @bodyParam request_type string Original request type: "Create" or "Update". Example: Create
     * @bodyParam dhis_uid string DHIS2 facility UID (required for group assignment retries). Example: abcd1234efg
     *
     * @response 302 scenario="Facility Created Successfully" {
     *   "message": "Facility created successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="Facility Updated Successfully" {
     *   "message": "Facility updated successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="Ownership Group Assigned" {
     *   "message": "Facility ownership group assigned successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="Level of Care Group Assigned" {
     *   "message": "Facility level of care group assigned successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="Ownership Assignment Failed" {
     *   "message": "Failed to assign facility ownership group",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="DHIS2 API Error" {
     *   "message": "Error creating facility in DHIS2!",
     *   "redirect": "back"
     * }
     *
     * @response 302 scenario="Facility Not Found in DHIS2" {
     *   "message": "Failed to get matching facility in DHIS2!",
     *   "redirect": "back"
     * }
     *
     * @apiResourceAdditional This endpoint handles four types of retries: facility creation, facility update, ownership group assignment, and level of care group assignments.
     * @apiResourceAdditional Each retry type is processed independently based on which status field is "Failed".
     */
    //try resending data to DHIS2 after failure
    public function resend(Request $request)
    {

        // dd($request->all());

        $dhis = new HfrDhis;
        $log = new DhisLog;
        $log = DhisLog::find($request->log_id);

        $hosp = new Hospital;
        $hosp = Hospital::find($request->facility_id);

        if ($request->facility_status == 'Failed') {
            $data = [];

            if ($hosp['operational_status_id'] > 1 && $hosp['operational_status_id'] < 5) {
                $close_date = Carbon::now()->format('Y-m-d');
            } elseif ($hosp['operational_status_id'] > 4) {
                $close_date = $hosp['close_date'];
            } else {
                $close_date = '';
            }

            $data = [
                'name' => $dhis->formatName($hosp['facility_name'], $hosp['state_id']),
                'shortName' => $dhis->getShortname($hosp['facility_name'], $hosp['alt_facility_name']),
                'code' => $request->facility_id,
                'openingDate' => $dhis->formatDate($hosp['start_date']),
                'closedDate' => $dhis->formatDate($close_date),
                'address' => $hosp['postal_address'],
                'coordinates' => $dhis->formatGeoCords($hosp['longitude'], $hosp['latitude']),
                'email' => $hosp['email_address'],
                'url' => $hosp['website'],
                'phoneNumber' => $hosp['phone_number'],
                'parent' => $dhis->getParent($hosp['ward_id'])
            ];

            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            // if request failed for new facility
            if ($request->request_type == 'Create') {

                try {
                    $response = $client->post('organisationUnits', [
                        'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                        'json' => $data
                    ]);

                    if ($response->getReasonPhrase() ==  'Created') {
                        $array = json_decode($response->getBody()->getContents(), true);

                        $facility_uid = $array['response']['uid'];

                        //Assign Organisation unit - ownership
                        $ownership_status = $dhis->assignOwnership($hosp['ownership_id'], $facility_uid);

                        //Assign Organisation unit - Level of Care
                        $level_status = $dhis->assignLevelOfCare($hosp['facility_level_id'], $facility_uid);

                        //Assign Organisation unit - Level of Care Options
                        if ($hosp['facility_level_option_id'] > 0) {
                            $level_option_status = $dhis->assignLevelOfCareOption($hosp['facility_level_option_id'], $facility_uid);
                        } else {
                            $level_option_status[0] = '';
                            $level_option_status[1] = '';
                        }


                        //Save status of actions
                        $error = '';
                        if ($ownership_status[0] == 'Failed') {
                            $error = 'Ownership assignment error: ' . $ownership_status[1];
                        }
                        if ($level_status[0] == 'Failed') {
                            $error = $error . ' Level of care assignment error: ' . $level_status[1];
                        }
                        if ($level_option_status[0] == 'Failed') {
                            $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
                        }

                        $log->dhis_uid = $facility_uid;
                        $log->facility_status = 'Created';
                        $log->ownership_status = $ownership_status[0];
                        $log->level_status = $level_status[0];
                        $log->level_option_status = $level_option_status[0];
                        $log->error_details = $error;
                        $log->save();

                        //send notification email to dhis team
                        $dhis->sendEmailtoDhisTeamForNewFacility($hosp['facility_name'], $hosp['ward_id']);

                        session()->flash("alert-success", "Facility created successfully!");
                        return back();
                    }
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $log->facility_status = 'Failed';
                        $log->error_details = $e->getResponse()->getBody()->getContents();
                        $log->save();

                        session()->flash("alert-danger", "Error creating facility in DHIS2!");
                        return back();
                    } else {
                        $log->facility_status = "Failed";
                        $log->error_details = 'No response from the server';
                        $log->save();

                        session()->flash("alert-danger", "Error creating facility in DHIS2!");
                        return back();
                    }
                }
            } //end if create
            //if the request failed was for update of facility
            elseif ($request->request_type == 'Update') {
                $uid = $dhis->getDhisFacilityUID($request->facility_id);

                if (strlen($uid) == 11) {
                    try {
                        $response = $client->put('organisationUnits/' . $uid, [
                            'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                            'json' => $data['updates']
                        ]);

                        $status = $response->getReasonPhrase();
                        if ($status == 'OK') {
                            $update_status = "Updated";
                        } else {
                            $update_status = $status;
                        }

                        $ownership_status[0] = '';
                        $level_status[0] = '';
                        $level_option_status[0] = '';

                        //assign organiation units
                        $dhis->unAssignOwnership($hosp['ownership_id'], $uid);
                        $ownership_status = $dhis->AssignOwnership($hosp['ownership_id'], $uid);
                        $dhis->unAssignLevelOfCare($hosp['facility_level_id'], $uid);
                        $level_status = $dhis->AssignLevelOfCare($hosp['facility_level_id'], $uid);

                        if ($hosp['facility_level_option_id'] > 0) {
                            $dhis->unAssignLevelOfCareOption($hosp['facility_level_option_id'], $uid);
                            $level_option_status = $dhis->AssignLevelOfCareOption($hosp['facility_level_option_id'], $uid);
                        } else {
                            $level_option_status[0] = '';
                        }

                        //Save status of actions to log
                        $error = '';
                        if ($ownership_status[0] == 'Failed') {
                            $error = 'Ownership assignment error: ' . $ownership_status[1];
                        }
                        if ($level_status[0] == 'Failed') {
                            $error = $error . ' Level of care assignment error: ' . $level_status[1];
                        }
                        if ($level_option_status[0] == 'Failed') {
                            $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
                        }

                        $log->dhis_uid = $uid;
                        $log->facility_status = $update_status;
                        $log->ownership_status = $ownership_status[0];
                        $log->level_status = $level_status[0];
                        $log->level_option_status = $level_option_status[0];
                        $log->error_details = $error;
                        $log->save();

                        //send notification email to dhis team
                        $dhis->sendEmailtoDhisTeamForUpdatedFacility($hosp['facility_name'], $hosp['ward_id']);

                        session()->flash("alert-success", "Facility updated successfully!");
                        return back();
                    } catch (RequestException $e) {
                        $log = new DhisLog;
                        if ($e->hasResponse()) {
                            $log->facility_status = 'Failed';
                            $log->error_details = $e->getResponse()->getBody()->getContents();
                            $log->save();

                            session()->flash("alert-danger", "Error creating facility in DHIS2!");
                            return back();
                        } else {
                            $log->facility_status = "Failed";
                            $log->error_details = 'No response from the server';
                            $log->save();

                            session()->flash("alert-danger", "Error creating facility in DHIS2!");
                            return back();
                        }
                    }
                } else { // if failed to get facility uid from dhis
                    $log->facility_status = "Failed";
                    $log->error_details = 'Failed to get matching facility in DHIS2';
                    $log->save();

                    session()->flash("alert-danger", "Failed to get matching facility in DHIS2!");
                    return back();
                }
            } //end if update

        } //end if Facility status failed

        if ($request->ownership_status == 'Failed') {
            $dhis->unAssignOwnership($hosp['ownership_id'], $request->dhis_uid);
            $ownership_status = $dhis->AssignOwnership($hosp['ownership_id'], $request->dhis_uid);

            $error = '';
            if ($ownership_status[0] == 'Failed') {
                $error = 'Ownership assignment error: ' . $ownership_status[1];
            }

            $log->ownership_status = $ownership_status[0];
            $log->error_details = $error;
            $log->save();

            if ($ownership_status[0] == 'Failed') {
                session()->flash("alert-danger", "Failed to assign facility ownership group");
                return back();
            } else {
                session()->flash("alert-success", "Facility ownership group assigned successfully!");
                return back();
            }
        }

        if ($request->level_status == 'Failed') {
            $dhis->unAssignLevelOfCare($hosp['facility_level_id'], $request->dhis_uid);
            $level_status = $dhis->AssignLevelOfCare($hosp['facility_level_id'], $request->dhis_uid);

            $error = '';
            if ($level_status[0] == 'Failed') {
                $error = $error . ' Level of care assignment error: ' . $level_status[1];
            }

            $log->level_status = $level_status[0];
            $log->error_details = $error;
            $log->save();

            if ($level_status[0] == 'Failed') {
                session()->flash("alert-danger", "Failed to assign facility level of care group");
                return back();
            } else {
                session()->flash("alert-success", "Facility level of care group assigned successfully!");
                return back();
            }
        }

        if ($request->level_option_status == 'Failed') {
            if ($hosp['facility_level_option_id'] > 0) {
                $dhis->unAssignLevelOfCareOption($hosp['facility_level_option_id'], $request->dhis_uid);
                $level_option_status = $dhis->AssignLevelOfCareOption($hosp['facility_level_option_id'], $request->dhis_uid);
            } else {
                $level_option_status[0] = '';
            }

            $error = '';
            if ($level_option_status[0] == 'Failed') {
                $error = $error . ' Level of care option assignment error: ' . $level_option_status[1];
            }

            $log->level_option_status = $level_option_status[0];
            $log->error_details = $error;
            $log->save();

            if ($level_option_status[0] == 'Failed') {
                session()->flash("alert-danger", "Failed to assign facility level of care group");
                return back();
            } else {
                session()->flash("alert-success", "Facility level of care group assigned successfully!");
                return back();
            }
        }
    }



    /**
     * Delete/Close Facility in DHIS2
     *
     * Attempts to delete a facility from DHIS2. If deletion fails due to associated data (DataValue),
     * automatically closes the facility instead by setting the closure date. Handles three scenarios:
     * 1. Successful deletion (no data associated)
     * 2. Closure (has associated data, cannot be deleted)
     * 3. Failure (other errors)
     * 
     * Sends appropriate email notifications to DHIS2 team. All operations are logged.
     *
     * @authenticated
     *
     * @bodyParam id integer required HFR facility ID. Example: 12345
     * @bodyParam facility_name string required Facility name for email notification. Example: General Hospital Abuja
     * @bodyParam alt_facility_name string Alternative facility name. Example: Gen Hosp Abuja
     * @bodyParam state_id integer required State ID. Example: 1
     * @bodyParam ward_id integer required Ward ID for email notification. Example: 50
     * @bodyParam start_date date Facility opening date. Example: 2020-01-15
     * @bodyParam postal_address string Postal address. Example: P.O. Box 123, Abuja
     * @bodyParam longitude string Longitude coordinate. Example: 7.3986
     * @bodyParam latitude string Latitude coordinate. Example: 9.0765
     * @bodyParam email_address string Email address. Example: info@hospital.com
     * @bodyParam website string Website URL. Example: https://hospital.com
     * @bodyParam phone_number string Phone number. Example: +234-123-456-7890
     *
     * @response 200 scenario="Successfully Deleted" "Deleted"
     *
     * @response 200 scenario="Closed (Cannot Delete - Has Data)" "Closed"
     *
     * @response 200 scenario="Deletion Failed" "Not Deleted"
     *
     * @response 200 scenario="Closure Failed" "Not Closed"
     *
     * @response 500 scenario="DHIS2 API Error" "Exception_Error"
     *
     * @response 500 scenario="Facility Not Found in DHIS2" "Exception_Error"
     *
     * @apiResourceAdditional Automatic closure: When deletion fails due to "Could not delete due to association with another object: DataValue" error, the facility is automatically closed with current date as closure date.
     * @apiResourceAdditional 502 Bad Gateway errors are interpreted as successful deletions (facility already removed).
     * @apiResourceAdditional Different email notifications sent for deletion vs closure scenarios.
     */
    public function delete(Request $request)
    {
        $dhis = new HfrDhis;
        $log = new DhisLog;
        $data = [];
        $fac_id = $request->id;
        $uid = $dhis->getDhisFacilityUID($fac_id);

        $close_date = Carbon::now()->format('Y-m-d');

        $data = [
            'name' => $dhis->formatName($request->facility_name, $request->state_id),
            'shortName' => $dhis->getShortname($request->facility_name, $request->alt_facility_name),
            'code' => $request->id,
            'openingDate' => $dhis->formatDate($request->start_date),
            'closedDate' => $dhis->formatDate($close_date),
            'address' => $request->postal_address,
            'coordinates' => $dhis->formatGeoCords($request->longitude, $request->latitude),
            'email' => $request->email_address,
            'url' => $request->website,
            'phoneNumber' => $request->phone_number,
            'parent' => $dhis->getParent($request->ward_id)
        ];

        if (strlen($uid) == 11) {
            try {
                $client = new Client([
                    'base_uri' =>  config('hfr.dhis_url')
                ]);

                $response = $client->delete('organisationUnits/' . $uid, [
                    'auth' => [config('hfr.dhis_username'), config('hfr.dhis_password')],
                ]);

                if ($response->getReasonPhrase() == 'OK') {
                    $log->hfr_id = $fac_id;
                    $log->dhis_uid = $uid;
                    $log->facility_status = "Deleted";
                    $log->error_details = "";
                    $log->request_type = 'Delete';
                    $log->user_id = Auth::user()->id;
                    $log->save();

                    $dhis->sendEmailtoDhisTeamForDeletedFacility($request->facility_name, $request->ward_id);

                    return 'Deleted';
                } else {
                    $log->hfr_id = $fac_id;
                    $log->dhis_uid = $uid;
                    $log->facility_status = "Not Deleted";
                    $log->error_details = "";
                    $log->request_type = 'Delete';
                    $log->user_id = Auth::user()->id;
                    $log->save();
                    return 'Not Deleted';
                }

                //send notification email to dhis team

            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $error = (string) $e->getResponse()->getBody()->getContents();

                    if (strpos($error, '502 Bad Gateway') !== false) { //facilited has beed deleted
                        $log->hfr_id = $fac_id;
                        $log->dhis_uid = $uid;
                        $log->facility_status = "Deleted";
                        $log->error_details = "";
                        $log->request_type = 'Delete';
                        $log->user_id = Auth::user()->id;
                        $log->save();

                        $dhis->sendEmailtoDhisTeamForDeletedFacility($request->facility_name, $request->ward_id);

                        return "Deleted";
                    } elseif (strpos($error, 'Could not delete due to association with another object: DataValue') !== false) {
                        //if the facility has data mark it as closed
                        $status = [];
                        $status[0] = '';
                        $status = $dhis->closeFacility($data, $uid);

                        if ($status[0] == 'Closed') {
                            $log->hfr_id = $fac_id;
                            $log->facility_status = 'Closed';
                            $log->error_details = '';
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();

                            $dhis->sendEmailtoDhisTeamForClosedFacility($request->facility_name, $request->ward_id);

                            return "Closed";
                        }
                        if ($status[0] == 'Not Closed') {
                            $log->hfr_id = $fac_id;
                            $log->facility_status = 'Not Closed';
                            $log->error_details = '';
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();
                            return "Not Closed";
                        }
                        if ($status[0] == 'Failed') {
                            $log->hfr_id = $fac_id;
                            $log->facility_status = $status[0];
                            $log->error_details = $status[1];
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();
                            return "Exception_Error";
                        }
                    } else {
                        $log->hfr_id = $fac_id;
                        $log->facility_status = "Failed";
                        $log->error_details = $error;
                        $log->user_id = Auth::user()->id;
                        $log->request_type = 'Delete';
                        $log->save();
                        return "Exception_Error";
                    }
                } else {
                    $log->hfr_id = $fac_id;
                    $log->facility_status = "Failed";
                    $log->error_details = 'No response from the server';
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Delete';
                    $log->save();
                    return "Exception_Error";
                }
            }
        } else { // if failed to get facility uid from dhis
            $log->hfr_id = $fac_id;
            $log->facility_status = "Failed";
            $log->error_details = 'Could not get a facility with code ' . $fac_id . ' in DHIS2';
            $log->user_id = Auth::user()->id;
            $log->save();
            return "Exception_Error";
        }
    }



    /**
     * View DHIS2 Operation Logs
     *
     * Displays a complete audit trail of all DHIS2 synchronization operations including
     * creates, updates, deletes, and group assignments. Shows status of each operation
     * and any error details for troubleshooting.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "view": "dhis.logs",
     *   "logs": [
     *     {
     *       "id": 1,
     *       "hfr_id": 12345,
     *       "dhis_uid": "abcd1234efg",
     *       "facility_status": "Created",
     *       "ownership_status": "Success",
     *       "level_status": "Success",
     *       "level_option_status": "Success",
     *       "error_details": "",
     *       "request_type": "Create",
     *       "user_id": 10,
     *       "created_at": "2025-11-15 10:30:00"
     *     },
     *     {
     *       "id": 2,
     *       "hfr_id": 12346,
     *       "dhis_uid": null,
     *       "facility_status": "Failed",
     *       "ownership_status": null,
     *       "level_status": null,
     *       "level_option_status": null,
     *       "error_details": "Connection timeout",
     *       "request_type": "Create",
     *       "user_id": 10,
     *       "created_at": "2025-11-15 11:00:00"
     *     }
     *   ]
     * }
     *
     * @apiResourceAdditional facility_status values: Created, Updated, Deleted, Closed, Failed, Not Deleted, Not Closed
     * @apiResourceAdditional Group status values: Success, Failed, or empty string (not applicable)
     */
    public function logs()
    {
       // $logs = DB::table('dhis_log_details')->paginate(100);

        /* $logs = DhisLog::all(); 

        return view('dhis.logs', compact("logs")); */

         $logs = DhisLog::with([
        'hospital.state',
        'hospital.lga',
        'hospital.ward',
        'hospital.ownership',
        'hospital.facilitylevelofcare',
        'hospital.facilitylevelofcareoption',
        'hospital.publishedby'
    ])
    ->orderByDesc('id')
    ->get();

    return view('dhis.logs', compact("logs"));

        /*   $logs = DB::table('dhis_log as l')
        ->leftJoin('hs_hospitals_history as h', 'h.id', '=', 'l.hfr_id')
        ->select(
            'l.*',
            'h.facility_name',
            'h.published_at'
        )
        ->orderByDesc('l.id')
        ->get();

        return view('dhis.logs', compact("logs")); */
    }



   /**
     * Display DHIS2 Lookup Table
     *
     * Shows the mapping/lookup table between HFR values and DHIS2 UIDs.
     * Used for translating HFR codes (ownership, levels, etc.) to DHIS2 organization unit groups.
     * Paginated at 15 records per page.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "view": "dhis.lookup",
     *   "lookup": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "type": "ownership",
     *         "hfr_id": 1,
     *         "hfr_description": "Public",
     *         "dhis_uid": "xyz123abc45",
     *         "dhis_description": "Public Ownership",
     *         "created_at": "2024-01-01 00:00:00",
     *         "updated_at": "2024-01-01 00:00:00"
     *       },
     *       {
     *         "id": 2,
     *         "type": "level",
     *         "hfr_id": 1,
     *         "hfr_description": "Primary",
     *         "dhis_uid": "abc456def78",
     *         "dhis_description": "Primary Care",
     *         "created_at": "2024-01-01 00:00:00",
     *         "updated_at": "2024-01-01 00:00:00"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 25
     *   }
     * }
     *
     * @apiResourceAdditional type field indicates the lookup category: ownership, level, level_option, parent (ward), etc.
     */
    public function lookupIndex()
    {
        $lookup = DhisLookup::paginate(15);
        return view('dhis.lookup', compact("lookup"));
    }


    /**
     * Create DHIS2 Lookup Entry
     *
     * Adds a new mapping between HFR values and DHIS2 UIDs.
     * DHIS2 UID must be unique across all lookup entries.
     *
     * @authenticated
     *
     * @bodyParam type string required Lookup type: ownership, level, level_option, parent. Example: ownership
     * @bodyParam hfr_id integer required HFR reference ID. Example: 1
     * @bodyParam hfr_description string required HFR description/label. Example: Public
     * @bodyParam dhis_uid string required Unique DHIS2 organization unit or group UID (11 characters). Example: xyz123abc45
     * @bodyParam dhis_description string DHIS2 description/label. Example: Public Ownership
     *
     * @response 302 scenario="Success" {
     *   "message": "Value added successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 422 scenario="Duplicate DHIS UID" {
     *   "message": "The dhis uid has already been taken.",
     *   "errors": {
     *     "dhis_uid": ["The dhis uid has already been taken."]
     *   }
     * }
     */
    public function lookupStore(Request $request)
    {
        $request->validate([
            'dhis_uid' => 'required|unique:dhis_lookup',
        ]);
        $lookup = new DhisLookup;
        $lookup->fill($request->all());
        $lookup->save();
        session()->flash("alert-success", "Value added successfully!");
        return back();
    }



    /**
     * Update DHIS2 Lookup Entry
     *
     * Updates an existing mapping between HFR values and DHIS2 UIDs.
     * Useful for correcting mappings or updating DHIS2 UIDs after system changes.
     *
     * @authenticated
     *
     * @bodyParam id integer required Lookup entry ID to update. Example: 5
     * @bodyParam type string required Lookup type: ownership, level, level_option, parent. Example: ownership
     * @bodyParam hfr_id integer required HFR reference ID. Example: 1
     * @bodyParam hfr_description string required HFR description/label. Example: Public
     * @bodyParam dhis_uid string required DHIS2 organization unit or group UID. Example: xyz123abc45
     * @bodyParam dhis_description string DHIS2 description/label. Example: Public Ownership
     *
     * @response 302 scenario="Success" {
     *   "message": "Value updated successfully!",
     *   "redirect": "back"
     * }
     *
     * @response 404 scenario="Not Found" {
     *   "message": "Lookup entry not found"
     * }
     */
    public function lookupUpdate(Request $request)
    {
        $lookup = new DhisLookup;
        $lookup = DhisLookup::find($request->id);
        $lookup->fill($request->all());
        $lookup->save();
        session()->flash("alert-success", "Value updated successfully!");
        return back();
    }



   /**
     * Search DHIS2 Lookup Entries
     *
     * Filters lookup table by type and HFR description.
     * Useful for finding specific mappings during configuration or troubleshooting.
     * Results are paginated at 15 per page.
     *
     * @authenticated
     *
     * @bodyParam type string required Lookup type to filter: ownership, level, level_option, parent. Example: ownership
     * @bodyParam hfr_description string HFR description search term. Partial matching supported. Example: Public
     *
     * @response 200 scenario="Found Results" {
     *   "view": "dhis.lookup",
     *   "lookup": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "type": "ownership",
     *         "hfr_id": 1,
     *         "hfr_description": "Public",
     *         "dhis_uid": "xyz123abc45",
     *         "dhis_description": "Public Ownership"
     *       }
     *     ],
     *     "per_page": 15,
     *     "total": 1
     *   }
     * }
     *
     * @response 200 scenario="No Results" {
     *   "lookup": {
     *     "data": [],
     *     "total": 0
     *   }
     * }
     */
    public function lookupSearch(Request $request)
    {
        $lookup = DhisLookup::where('type', $request->type)
            ->where('hfr_description', 'like', '%' .  $request->hfr_description . '%')
            ->paginate(15)
            ->appends($request->all());


        $request->flash('request', $request);
        return view('dhis.lookup', compact("lookup"));
    }
}
