<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\User;
use Notification;
use App\Notifications\sendNewFacilityEmailtoDhisTeam;
use App\Notifications\sendUpdateFacilityEmailtoDhisTeam;
use App\Notifications\sendDeleteFacilityEmailtoDhisTeam;
use Carbon\Carbon;



class HfrDhis extends Model
{
    public function getParent($ward_id){
        $uid = $this->getWardUID($ward_id);
        
        $parent = [];
        $parent['id'] = $uid;

        return $parent;
    }

    public function getWardUID($ward_id){
        $ward = DB::table('dhis_lookup')
        ->select('dhis_uid')
        ->where('hfr_id',$ward_id)
        ->where('type','Ward')   
        ->get();

        return $ward[0]->dhis_uid;
    }

    //get the shortname for dhis, if the name is longer than 50, take 
    //first 50 char of the name. and if alt name is not supplied use name for shortname
    public function getShortname($name, $alt_name){
        if ($alt_name ==''){
            $shortname = $this->toDhisShortName($name);
        }
        else{
            $shortname = $this->toDhisShortName($alt_name);
        }
        return $shortname;
    }

    public function toDhisShortName($name){
        if(strlen($name) > 49){
            $shortname = substr($name,0,50);
        }else{
            $shortname = $name;
        }
        return $shortname;
    }

    public function formatName($name,$state_id){
        $code = DB::table('ou_states')
            ->select('short_code')
            ->where('id',$state_id)
            ->get();

        $shortcode = strtolower($code[0]->short_code);
        $facility_name = $shortcode. " ". $name;
        return $facility_name;
    }

    public function formatGeoCords($long, $lat){
        return '['. $long. ','. $lat . ']';
    }

    public function formatDate($date){
        if ($date == ''){
            return "";
        }else{
            return date('Y-m-d', strtotime($date));
        }
    }

    //ownership i.e. private and public
    public function assignOwnership($ownership_id,$orgUnit){     
        $data= [];
        try{
            //get ownership uid
            $ownership = DB::table('dhis_lookup')
            ->select('dhis_uid')
            ->where('hfr_id',$ownership_id)
            ->where('type','Ownership')   
            ->get();

            $orgUnitGroup = $ownership[0]->dhis_uid;

            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response=$client->post('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);
            
            if ($response->getStatusCode() == '204'){
                $data[0] = 'Assigned';
            }else{
                $data[0] = 'Not assigned';
            }
            $data[1] = '';
            return $data;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data[0] = 'Failed';
                $data[1] =  $e->getResponse()->getBody()->getContents();
                return $data;
            }else {
                $data[0] = 'Failed';
                $data[1] = 'No response from the server';
                return $data;
            }
        }
    }

    public function unAssignOwnership($ownership_id,$orgUnit){      
        try{
            //get ownership uid
            $ownership = DB::table('dhis_lookup')
            ->select('dhis_uid')
            ->where('hfr_id',$ownership_id)
            ->where('type','Ownership')   
            ->get();

            $orgUnitGroup = $ownership[0]->dhis_uid;

            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);
    
            $response=$client->delete('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);
            
            if ($response->getStatusCode() == '204'){
                return 'true';
            }else{
                return 'false';
            }
            
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse()->getBody()->getContents();                            
            }else {
                return "Exception_Error";
            }
        }
    }

    // level of care, Primary, Secodary & Tertiary
    public function assignLevelOfCare($levelId,$orgUnit){
        $data = [];
        try{
            //get level of care uid
            $level = DB::table('dhis_lookup')
                    ->select('dhis_uid')
                    ->where('hfr_id',$levelId)
                    ->where('type','Level of Care')   
                    ->get();

            $orgUnitGroup = $level[0]->dhis_uid;
        
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response=$client->post('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);

            if ($response->getStatusCode() == '204'){
                $data[0] = 'Assigned';
            }else{
                $data[0] = 'Not Assigned';
            }
            $data[1] = '';
            return $data;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data[0] = 'Failed';
                $data[1] =  $e->getResponse()->getBody()->getContents();
                return $data;
            }else {
                $data[0] = 'Failed';
                $data[1] = 'No response from the server';
                return $data;
            }
        }
    }

    public function unAssignLevelOfCare($levelId,$orgUnit){
        try{
            //get level of care uid
            $level = DB::table('dhis_lookup')
                    ->select('dhis_uid')
                    ->where('hfr_id',$levelId)
                    ->where('type','Level of Care')   
                    ->get();

            $orgUnitGroup = $level[0]->dhis_uid;
        
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response=$client->delete('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);

            if ($response->getStatusCode() == '204'){
                return 'true';
            }else{
                return 'false';
            }

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse()->getBody()->getContents();               
            }else {
                return "Exception_Error";
            }
        }
    }

    // Level of care option eg. Health Post, Primary Health Center etc.
    public function assignLevelOfCareOption($levelOptionId,$orgUnit){
        try{
            //get level of care option uid
            $levelOption = DB::table('dhis_lookup')
                    ->select('dhis_uid')
                    ->where('hfr_id',$levelOptionId)
                    ->where('type','Level of Care Option')   
                    ->get();

            $orgUnitGroup = $levelOption[0]->dhis_uid;
        
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response=$client->post('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);

            if ($response->getStatusCode() == '204'){
                $data[0] = 'Assigned';
            }else{
                $data[0] = 'Not Assigned';
            }

            $data[1] = '';
            return $data;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data[0] = 'Failed';
                $data[1] =  $e->getResponse()->getBody()->getContents();
                return $data;
            }else {
                $data[0] = 'Failed';
                $data[1] = 'No response from the server';
                return $data;
            }
        }
    
    }

    public function unAssignLevelOfCareOption($levelOptionId,$orgUnit){
        try{
            //get level of care option uid
            $levelOption = DB::table('dhis_lookup')
                    ->select('dhis_uid')
                    ->where('hfr_id',$levelOptionId)
                    ->where('type','Level of Care Option')   
                    ->get();

            $orgUnitGroup = $levelOption[0]->dhis_uid;
        
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);
            
            $response=$client->delete('organisationUnitGroups/'. $orgUnitGroup .'/organisationUnits/'. $orgUnit, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
            ]);

            if ($response->getStatusCode() == '204'){
                return 'true';
            }else{
                return 'false';
            }

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return Psr7\str($e->getResponse());               
            }else {
                return "Exception_Error";
            }
        }
    
    }

    public function getDhisFacilityUID($hfr_facility_id){      
        try{
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);
    
            $response = $client->get('organisationUnits?filter=code:eq:'. $hfr_facility_id , [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')]
            ]);

    
            $array = json_decode($response->getBody()->getContents(), true); 
         
            if ($array['pager']['total'] > 0){
                $orgUnits = $array['organisationUnits'][0]['id'];
            }else{
                $orgUnits = 'Facility with id '. $hfr_facility_id. ' was not found in DHIS2' ;
            }
        
            return $orgUnits;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response =  Psr7\str($e->getResponse());
                return $response;
            }else {
                return "Operation failed due to network error!";
            }
        }
    }    

    public function getDhisUpdatedValues($hosp,$id){
        $audit_id = DB::table('audits')
            ->select('id')
            ->where('event', '=', 'updated')
            ->where('auditable_type','=','App\HospitalHistory')
            ->where('auditable_id','=',$id)
            ->orderBy('id', 'DESC')
            ->first();

        $hosp = HospitalHistory::find($id);
        $audit = $hosp->audits()->find($audit_id->id);
        $allUpdatedValues= $audit->getModified();

        $dhisFields = ["facility_name","alt_facility_name","start_date","close_date","postal_address","email_address","website",
          "phone_number","longitude","latitude","ownership_id","facility_level_id","facility_level_option_id","operational_status_id"];
        
        $dhisUpdatedFields = array_intersect($dhisFields, array_keys($allUpdatedValues));
        
  
        if (count($dhisUpdatedFields) > 0) {  //there is at least one dhis field updated
            $dhis = new HfrDhis;

            $data = [];
            $orgUnitGroups =[];
            $dataArray = [];
            
           
            $close_date = $hosp['close_date'];

            foreach($allUpdatedValues as $key => $value) {
                if(in_array($key,$dhisUpdatedFields )){
                    switch ($key) {
                        case "ownership_id":
                            $orgUnitGroups['ownership_id'] = $value['new'];                          
                            break;
                        case "facility_level_id":
                            $orgUnitGroups['facility_level_id'] = $value['new'];                            
                            break;
                        case "facility_level_option_id":
                            $orgUnitGroups['facility_level_option_id'] = $value['new'];                          
                            break;
                        case "operational_status_id":
                            if($hosp->operational_status_id > 1 && $hosp->operational_status_id < 5){
                                $close_date= Carbon::now()->format('Y-m-d');  
                            }
                            elseif($hosp->operational_status_id > 4){
                                $close_date = $hosp['close_date'];
                            }else{
                                $close_date = '';
                            }            
                            break;
                    }
                    
                }
            }

            $data = [
                'name' =>$dhis->formatName($hosp['facility_name'], $hosp['state_id']),
                'shortName' => $dhis->getShortname($hosp['facility_name'],$hosp['alt_facility_name']),
                'code' => $id,
                'openingDate' => $dhis->formatDate($hosp['start_date']),
                'closedDate' =>$dhis->formatDate($close_date),
                'address' => $hosp['postal_address'],
                'coordinates' => $dhis->formatGeoCords($hosp['longitude'],$hosp['latitude']),
                'email' => $hosp['email_address'],
                'url' => $hosp['website'],
                'phoneNumber' => $hosp['phone_number'],
                'parent' => $dhis->getParent($hosp['ward_id'])
            ];
            
            $dataArray['updates'] = $data;
            $dataArray['facility_name'] = $hosp['facility_name'];
            $dataArray['ward_id'] = $hosp['ward_id'];
            
            if (count($orgUnitGroups) > 0){
                $dataArray['groups'] = $orgUnitGroups;
            }else{
                $dataArray['groups'] = 'empty';
            }

            return $dataArray;
        
        }else{
            return false;
        }

    }

    public function closeFacility($data,$uid){
        try{
     
            $client = new Client([
                'base_uri' =>  config('hfr.dhis_url')
            ]);

            $response = $client->put('organisationUnits/'. $uid, [
                'auth' => [config('hfr.dhis_username'),config('hfr.dhis_password')],
                'json' => $data
            ]);

      
            if ( $response->getReasonPhrase() == 'OK'){
                $data[0] = 'Closed';                
            }else{
                $data[0] = 'Not Closed';                                
            }

            $data[1] = '';
            return $data;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data[0] = 'Failed';
                $data[1] =  $e->getResponse()->getBody()->getContents();
                return $data;
            }else {
                $data[0] = 'Failed';
                $data[1] = 'No response from the server';
                return $data;
            }
        }
    
    }


    public function sendEmailtoDhisTeamForNewFacility($name,$ward_id){
        $ward = DB::table('wards')
                ->where('id',$ward_id)
                ->get();

        $state = $ward[0]->state;
        $lga = $ward[0]->lga;
        $ward_name = $ward[0]->name;

        $users = User::permission('Receive DHIS2 Notifications')->get();
        Notification::send($users, new sendNewFacilityEmailtoDhisTeam($name,$state,$lga,$ward_name));
    }

    public function sendEmailtoDhisTeamForUpdatedFacility($name,$ward_id){
        $ward = DB::table('wards')
                ->where('id',$ward_id)
                ->get();

        $state = $ward[0]->state;
        $lga = $ward[0]->lga;
        $ward_name = $ward[0]->name;

        $users = User::permission('Receive DHIS2 Notifications')->get();
        Notification::send($users, new sendUpdateFacilityEmailtoDhisTeam($name,$state,$lga,$ward_name));
    }

    public function sendEmailtoDhisTeamForDeletedFacility($name,$ward_id){
        $ward = DB::table('wards')
                ->where('id',$ward_id)
                ->get();

        $state = $ward[0]->state;
        $lga = $ward[0]->lga;
        $ward_name = $ward[0]->name;

        $users = User::permission('Receive DHIS2 Notifications')->get();
        Notification::send($users, new sendDeleteFacilityEmailtoDhisTeam($name,$state,$lga,$ward_name));
    }

    public function sendEmailtoDhisTeamForClosedFacility($name,$ward_id){
        $ward = DB::table('wards')
                ->where('id',$ward_id)
                ->get();

        $state = $ward[0]->state;
        $lga = $ward[0]->lga;
        $ward_name = $ward[0]->name;

        $users = User::permission('Receive DHIS2 Notifications')->get();
        Notification::send($users, new sendCloseFacilityEmailtoDhisTeam($name,$state,$lga,$ward_name));
    }

}
