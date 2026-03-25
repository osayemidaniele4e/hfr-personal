<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\DhisLog;
use App\HfrDhis;
use App\DhisLookup;
use Auth;
use Carbon\Carbon;
use App\Hospital;



class HfrDhisController extends Controller
{
 
    public function store(Request $request){
        $dhis = new HfrDhis;

        $data= [];
       
        if($request->operational_status_id > 1 && $request->operational_status_id < 5){
            $close_date= Carbon::now()->format('Y-m-d');  
        }
        elseif($request->operational_status_id > 4){
            $close_date = $request->close_date;
        }else{
            $close_date = '';
        }

        $data = [
            'name' =>$dhis->formatName($request->facility_name, $request->state_id),
            'shortName' => $dhis->getShortname($request->facility_name,$request->alt_facility_name),
            'code' => $request->id,
            'openingDate' => $dhis->formatDate($request->start_date),
            'closedDate' =>$dhis->formatDate($close_date),
            'address' => $request->postal_address,
            'coordinates' => $dhis->formatGeoCords($request->longitude,$request->latitude),
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
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                'json' => $data
            ]);


            if ($response->getReasonPhrase() ==  'Created'){
                $array = json_decode($response->getBody()->getContents(), true); 
              
                $facility_uid = $array['response']['uid'];
           
                //Assign Organisation unit - ownership
                $ownership_status = $dhis->assignOwnership($request->ownership_id,$facility_uid);

                //Assign Organisation unit - Level of Care
                $level_status = $dhis->assignLevelOfCare($request->facility_level_id, $facility_uid);
       
                //Assign Organisation unit - Level of Care Options
                if ($request->facility_level_option_id > 0){
                    $level_option_status = $dhis->assignLevelOfCareOption($request->facility_level_option_id,$facility_uid);
                }else{
                    $level_option_status[0]='';
                }
                

                //Save status of actions
                $error = '';
                if ($ownership_status[0]=='Failed'){
                    $error = 'Ownership assignment error: '.$ownership_status[1];
                }
                if ($level_status[0]=='Failed'){
                    $error = $error. ' Level of care assignment error: '.$level_status[1];
                }
                if ($level_option_status[0]=='Failed'){
                    $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
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
                $log->facility_status = 'Failed' ;
                $log->error_details = $e->getResponse()->getBody()->getContents();
                $log->user_id = Auth::user()->id;
                $log->request_type = 'Create';
                $log->save();
                return "Exception_Error";
            }else {
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

    public function store2(){
        $dhis = new HfrDhis;

        $data= [];
        $parent = [];
        $parent['id'] = 'allHBmjrOUA';
        
        $data = [
            'name' =>'Beatus Test Facility',
            'shortName' => 'Beatus TF',
            // 'code' => '12456',
            'openingDate' =>'2019-05-05',
            'closedDate' =>'',
            'address' => 'Box 777 Dar',
            'email' =>'hfr@healt.com',
            // 'url' => 'www.hfr.com',
            'phoneNumber' => '12345678',
            'parent' => $parent
        ];
       
        try {
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);
    
            $response = $client->post('organisationUnits', [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                'json' => $data
            ]);


            if ($response->getReasonPhrase() ==  'Created'){
                $array = json_decode($response->getBody()->getContents(), true); 
              
                $facility_uid = $array['response']['uid'];
           
                //Assign Organisation unit - ownership
                $ownership_status = $dhis->assignOwnership('1',$facility_uid);

                //Assign Organisation unit - Level of Care
                $level_status = $dhis->assignLevelOfCare('1', $facility_uid);
       
                //Assign Organisation unit - Level of Care Options
                if (in_array('3',[1,2,5])){
                    $level_option_status = $dhis->assignLevelOfCareOption('5',$facility_uid);
                }
                else{
                    $level_option_status[0] = '';
                }

                //Save status of actions to log
                $error = '';
                if ($ownership_status[0]=='Failed'){
                    $error = 'Ownership assignment error: '.$ownership_status[1];
                }
                if ($level_status[0]=='Failed'){
                    $error = $error. ' Level of care assignment error: '.$level_status[1];
                }
                if ($level_option_status[0]=='Failed'){
                    $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
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
                $log->facility_status = 'Failed' ;
                $log->error_details = $e->getResponse()->getBody()->getContents() ;
                $log->user_id = Auth::user()->id;
                $log->save();
                dd("Exception_Error1");
            }else {
                $log->hfr_id = '71959570';
                $log->facility_status = "Failed";
                $log->error_details = 'No response from the server';
                $log->user_id = Auth::user()->id;
                $log->save();
                dd("Exception_Error2");
            }
        }
       
    }

    public function update(Request $request){
        $dhis = new HfrDhis;
        $id = $request->id;
        $data = $request->data;

        $uid = $dhis->getDhisFacilityUID($id);
        

        if(strlen($uid) == 11){
            try {
                $client = new Client([
                    'base_uri' =>  config('hfr.dhis_url')
                ]);
        
                $response = $client->put('organisationUnits/'. $uid, [
                    'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                    'json' => $data['updates']
                ]);
                
                $status = $response->getReasonPhrase();
                if ($status == 'OK'){
                    $update_status = "Updated";
                }else{
                    $update_status = $status;
                }

    
                $ownership_status[0] = '';
                $level_status[0] = '';
                $level_option_status[0] = '';

                //if any of the organiation groups is updated
                if ($data['groups'] != 'empty'){
                 
                    foreach($data['groups'] as $key => $value) {
                        switch ($key) {
                            case "ownership_id":
                                $dhis->unAssignOwnership($value,$uid);
                                $ownership_status = $dhis->AssignOwnership($value,$uid);
                                break;
                            case "facility_level_id":
                                $dhis->unAssignLevelOfCare($value,$uid);
                                $level_status = $dhis->AssignLevelOfCare($value,$uid);
                                break;
                            case "facility_level_option_id":
                                if ($value > 0){ 
                                    $dhis->unAssignLevelOfCareOption($value,$uid);
                                    $level_option_status = $dhis->AssignLevelOfCareOption($value,$uid);
                                }else{
                                    $level_option_status[0]='';
                                }      
                                break;
                        }
                    }
                }
    
                //Save status of actions to log
                $error = '';
                if ($ownership_status[0]=='Failed'){
                    $error = 'Ownership assignment error: '.$ownership_status[1];
                }
                if ($level_status[0]=='Failed'){
                    $error = $error. ' Level of care assignment error: '.$level_status[1];
                }
                if ($level_option_status[0]=='Failed'){
                    $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
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
                    $log->facility_status = 'Failed' ;
                    $log->error_details = $e->getResponse()->getBody()->getContents();
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Update';
                    $log->save();
                    return "Exception_Error";
                }else {
                    $log->hfr_id = $request->id;
                    $log->facility_status = "Failed";
                    $log->error_details = 'No response from the server';
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Update';
                    $log->save();
                    return "Exception_Error";
                }
            }

        }else { // if failed to get facility uid from dhis
            $log = new DhisLog;
            $log->hfr_id = $id;
            $log->facility_status = "Failed";
            $log->error_details = 'Failed to get matching facility in DHIS2';
            $log->user_id = Auth::user()->id;
            $log->save();
            return "Exception_Error";
        }
       
    }

    //try resending data to DHIS2 after failure
    public function resend(Request $request){

        // dd($request->all());

        $dhis = new HfrDhis;
        $log = new DhisLog;
        $log = DhisLog::find($request->log_id);

        $hosp = new Hospital; 
        $hosp= Hospital::find($request->facility_id);

        if ($request->facility_status == 'Failed'){
            $data= [];
       
            if($hosp['operational_status_id'] > 1 && $hosp['operational_status_id'] < 5){
                $close_date= Carbon::now()->format('Y-m-d');  
            }
            elseif($hosp['operational_status_id'] > 4){
                $close_date = $hosp['close_date'];
            }else{
                $close_date = '';
            }

            $data = [
                'name' =>$dhis->formatName($hosp['facility_name'], $hosp['state_id']),
                'shortName' => $dhis->getShortname($hosp['facility_name'],$hosp['alt_facility_name']),
                'code' => $request->facility_id,
                'openingDate' => $dhis->formatDate($hosp['start_date']),
                'closedDate' =>$dhis->formatDate($close_date),
                'address' => $hosp['postal_address'],
                'coordinates' => $dhis->formatGeoCords($hosp['longitude'],$hosp['latitude']),
                'email' => $hosp['email_address'],
                'url' => $hosp['website'],
                'phoneNumber' => $hosp['phone_number'],
                'parent' => $dhis->getParent($hosp['ward_id'])
            ];

            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            // if request failed for new facility
            if ($request->request_type == 'Create'){ 

                try {
                    $response = $client->post('organisationUnits', [
                        'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                        'json' => $data
                    ]);

                    if ($response->getReasonPhrase() ==  'Created'){
                        $array = json_decode($response->getBody()->getContents(), true); 
                      
                        $facility_uid = $array['response']['uid'];
                   
                        //Assign Organisation unit - ownership
                        $ownership_status = $dhis->assignOwnership($hosp['ownership_id'],$facility_uid);
        
                        //Assign Organisation unit - Level of Care
                        $level_status = $dhis->assignLevelOfCare($hosp['facility_level_id'], $facility_uid);
               
                        //Assign Organisation unit - Level of Care Options
                        if ($hosp['facility_level_option_id'] > 0){
                            $level_option_status = $dhis->assignLevelOfCareOption($hosp['facility_level_option_id'],$facility_uid);
                        }else{
                            $level_option_status[0]='';
                            $level_option_status[1]='';
                        }
                        
        
                        //Save status of actions
                        $error = '';
                        if ($ownership_status[0]=='Failed'){
                            $error = 'Ownership assignment error: '.$ownership_status[1];
                        }
                        if ($level_status[0]=='Failed'){
                            $error = $error. ' Level of care assignment error: '.$level_status[1];
                        }
                        if ($level_option_status[0]=='Failed'){
                            $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
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
                        $log->facility_status = 'Failed' ;
                        $log->error_details = $e->getResponse()->getBody()->getContents();
                        $log->save();

                        session()->flash("alert-danger", "Error creating facility in DHIS2!");        
                        return back();
                    }else {
                        $log->facility_status = "Failed";
                        $log->error_details = 'No response from the server';
                        $log->save();
                          
                        session()->flash("alert-danger", "Error creating facility in DHIS2!");        
                        return back();
                    }
                }
            }//end if create
            //if the request failed was for update of facility
            elseif($request->request_type == 'Update'){
                $uid = $dhis->getDhisFacilityUID($request->facility_id);

                if(strlen($uid) == 11){
                    try {
                        $response = $client->put('organisationUnits/'. $uid, [
                            'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                            'json' => $data['updates']
                        ]);
                        
                        $status = $response->getReasonPhrase();
                        if ($status == 'OK'){
                            $update_status = "Updated";
                        }else{
                            $update_status = $status;
                        }

                        $ownership_status[0] = '';
                        $level_status[0] = '';
                        $level_option_status[0] = '';

                        //assign organiation units
                        $dhis->unAssignOwnership($hosp['ownership_id'],$uid);
                        $ownership_status = $dhis->AssignOwnership($hosp['ownership_id'],$uid);
                        $dhis->unAssignLevelOfCare($hosp['facility_level_id'],$uid);
                        $level_status = $dhis->AssignLevelOfCare($hosp['facility_level_id'],$uid);
                       
                        if ($hosp['facility_level_option_id'] > 0){
                            $dhis->unAssignLevelOfCareOption($hosp['facility_level_option_id'],$uid);
                            $level_option_status = $dhis->AssignLevelOfCareOption($hosp['facility_level_option_id'],$uid);
                        }else{
                            $level_option_status[0]='';
                        }
            
                        //Save status of actions to log
                        $error = '';
                        if ($ownership_status[0]=='Failed'){
                            $error = 'Ownership assignment error: '.$ownership_status[1];
                        }
                        if ($level_status[0]=='Failed'){
                            $error = $error. ' Level of care assignment error: '.$level_status[1];
                        }
                        if ($level_option_status[0]=='Failed'){
                            $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
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
                            $log->facility_status = 'Failed' ;
                            $log->error_details = $e->getResponse()->getBody()->getContents();
                            $log->save();
                              
                            session()->flash("alert-danger", "Error creating facility in DHIS2!");        
                            return back();
                        }else {
                            $log->facility_status = "Failed";
                            $log->error_details = 'No response from the server';
                            $log->save();
                              
                            session()->flash("alert-danger", "Error creating facility in DHIS2!");        
                            return back();
                        }
                    }

                }else { // if failed to get facility uid from dhis
                    $log->facility_status = "Failed";
                    $log->error_details = 'Failed to get matching facility in DHIS2';
                    $log->save();
                      
                    session()->flash("alert-danger", "Failed to get matching facility in DHIS2!");        
                    return back();
                }
                      
            }//end if update

        }//end if Facility status failed       

        if ($request->ownership_status == 'Failed'){
            $dhis->unAssignOwnership($hosp['ownership_id'],$request->dhis_uid);
            $ownership_status = $dhis->AssignOwnership($hosp['ownership_id'],$request->dhis_uid);
            
            $error = '';
            if ($ownership_status[0]=='Failed'){
                $error = 'Ownership assignment error: '.$ownership_status[1];
            }
          
            $log->ownership_status = $ownership_status[0];
            $log->error_details = $error;
            $log->save();

            if ($ownership_status[0]=='Failed'){
                session()->flash("alert-danger", "Failed to assign facility ownership group");        
                return back();
            }else{
                session()->flash("alert-success", "Facility ownership group assigned successfully!");        
                return back();   
            }
        }

        if ($request->level_status == 'Failed'){
            $dhis->unAssignLevelOfCare($hosp['facility_level_id'],$request->dhis_uid);
            $level_status = $dhis->AssignLevelOfCare($hosp['facility_level_id'],$request->dhis_uid);

            $error = '';
            if ($level_status[0]=='Failed'){
                $error = $error. ' Level of care assignment error: '.$level_status[1];
            }
           
            $log->level_status = $level_status[0];
            $log->error_details = $error;
            $log->save();

            if ($level_status[0]=='Failed'){
                session()->flash("alert-danger", "Failed to assign facility level of care group");        
                return back();
            }else{
                session()->flash("alert-success", "Facility level of care group assigned successfully!");        
                return back();   
            }
        }

        if ($request->level_option_status == 'Failed'){
            if ($hosp['facility_level_option_id'] > 0){
                $dhis->unAssignLevelOfCareOption($hosp['facility_level_option_id'],$request->dhis_uid);
                $level_option_status = $dhis->AssignLevelOfCareOption($hosp['facility_level_option_id'],$request->dhis_uid);
            }else{
                $level_option_status[0]='';
            }

            $error = '';
            if ($level_option_status[0]=='Failed'){
                $error = $error. ' Level of care option assignment error: '.$level_option_status[1];
            }

            $log->level_option_status = $level_option_status[0];
            $log->error_details = $error;
            $log->save();

            if ($level_option_status[0]=='Failed'){
                session()->flash("alert-danger", "Failed to assign facility level of care group");        
                return back();
            }else{
                session()->flash("alert-success", "Facility level of care group assigned successfully!");        
                return back();   
            }
        }


   
       
       
       
    }

  
    public function delete(Request $request){
        $dhis = new HfrDhis;
        $log = new DhisLog;
        $data = [];
        $fac_id = $request->id;
        $uid = $dhis->getDhisFacilityUID($fac_id );
        
        $close_date= Carbon::now()->format('Y-m-d');

        $data = [
            'name' =>$dhis->formatName($request->facility_name, $request->state_id),
            'shortName' => $dhis->getShortname($request->facility_name,$request->alt_facility_name),
            'code' => $request->id,
            'openingDate' => $dhis->formatDate($request->start_date),
            'closedDate' =>$dhis->formatDate($close_date),
            'address' => $request->postal_address,
            'coordinates' => $dhis->formatGeoCords($request->longitude,$request->latitude),
            'email' => $request->email_address,
            'url' => $request->website,
            'phoneNumber' => $request->phone_number,
            'parent' => $dhis->getParent($request->ward_id)
        ];

        if(strlen($uid) == 11){
            try {
                $client = new Client([
                    'base_uri' =>  config('hfr.dhis_url')
                ]);
        
                $response=$client->delete('organisationUnits/'. $uid, [
                    'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                ]);
                
                if ($response->getReasonPhrase() == 'OK'){
                    $log->hfr_id = $fac_id;
                    $log->dhis_uid = $uid;
                    $log->facility_status = "Deleted";
                    $log->error_details = "";
                    $log->request_type = 'Delete';
                    $log->user_id = Auth::user()->id;
                    $log->save();

                    $dhis->sendEmailtoDhisTeamForDeletedFacility($request->facility_name, $request->ward_id);

                    return 'Deleted';
                }else{
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

                    if (strpos($error, '502 Bad Gateway') !== false){ //facilited has beed deleted
                        $log->hfr_id = $fac_id;
                        $log->dhis_uid = $uid;
                        $log->facility_status = "Deleted";
                        $log->error_details = "";
                        $log->request_type = 'Delete';
                        $log->user_id = Auth::user()->id;
                        $log->save();

                        $dhis->sendEmailtoDhisTeamForDeletedFacility($request->facility_name, $request->ward_id);

                        return "Deleted";
                    }
                    elseif (strpos($error, 'Could not delete due to association with another object: DataValue') !== false){
                        //if the facility has data mark it as closed
                        $status = [];
                        $status[0] ='';
                        $status = $dhis->closeFacility($data,$uid);

                        if ($status[0] =='Closed'){
                            $log->hfr_id = $fac_id;
                            $log->facility_status = 'Closed' ;
                            $log->error_details = '';
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();
                            
                            $dhis->sendEmailtoDhisTeamForClosedFacility($request->facility_name, $request->ward_id);

                            return "Closed";
                        }
                        if ($status[0]=='Not Closed'){
                            $log->hfr_id = $fac_id;
                            $log->facility_status = 'Not Closed' ;
                            $log->error_details = '';
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();
                            return "Not Closed";
                        }
                        if ($status[0]=='Failed'){
                            $log->hfr_id = $fac_id;
                            $log->facility_status = $status[0] ;
                            $log->error_details = $status[1];
                            $log->user_id = Auth::user()->id;
                            $log->request_type = 'Delete';
                            $log->save();
                            return "Exception_Error";
                        }
                                           
                    }else{
                        $log->hfr_id = $fac_id;
                        $log->facility_status = "Failed" ;
                        $log->error_details = $error;
                        $log->user_id = Auth::user()->id;
                        $log->request_type = 'Delete';
                        $log->save();
                        return "Exception_Error";
                    }

                }else {
                    $log->hfr_id = $fac_id;
                    $log->facility_status = "Failed";
                    $log->error_details = 'No response from the server';
                    $log->user_id = Auth::user()->id;
                    $log->request_type = 'Delete';
                    $log->save();
                    return "Exception_Error";
                }
            }

        }else { // if failed to get facility uid from dhis
            $log->hfr_id = $fac_id;
            $log->facility_status = "Failed";
            $log->error_details = 'Could not get a facility with code '. $fac_id .' in DHIS2';
            $log->user_id = Auth::user()->id;
            $log->save();
            return "Exception_Error";
        }

     
       
    }


    public function logs(){
        $logs = DB::table('dhis_log_details')->paginate(100);

        return view('dhis.logs', compact("logs")); 
    }

    public function lookupIndex(){
        $lookup = DhisLookup::paginate(15);
        return view('dhis.lookup', compact("lookup")); 
    }

    public function lookupStore(Request $request){
        $request->validate([
            'dhis_uid' => 'required|unique:dhis_lookup',
        ]);
        $lookup = new DhisLookup;
        $lookup->fill($request->all());
        $lookup->save();
        session()->flash("alert-success", "Value added successfully!");        
        return back();
    }

    public function lookupUpdate(Request $request){
        $lookup = new DhisLookup;
        $lookup = DhisLookup::find($request->id);
        $lookup->fill($request->all());
        $lookup->save();
        session()->flash("alert-success", "Value updated successfully!");        
        return back();
    }

    public function lookupSearch(Request $request){
        $lookup = DhisLookup::where('type',$request->type)
                ->where('hfr_description', 'like', '%' .  $request->hfr_description . '%')
                ->paginate(15)
                ->appends($request->all());

        
        $request->flash('request',$request);
        return view('dhis.lookup', compact("lookup")); 
    }

    
}
