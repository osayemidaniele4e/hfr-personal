<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\StatusTracking;
use Auth;
use Carbon\Carbon;
use App\Hospital;
use App\HospitalHistory;
use App\HospitalService;
use App\HospitalServiceHistory;
use App\audit;
use App\ApprovalNotifications;
use App\HfrDhis;

class PublishController extends Controller
{
    public function index()
    {
        $pending = DB::table('hospital_details_history')
            ->where('state_id', 'like', '%' .  Auth::user()->state_id . '%')
            ->whereIn('status_id',[4,11,18])
            ->get();

        
        return view('approvals.pending_publish',compact('pending'));
    }

    public function search(Request $request)
    {
        if ($request->status ==1){
            $pending = DB::table('hospital_details_history')
            ->where('state_id', 'like', '%' .  $request->state_id . '%')
            ->where('action', 'like', '%' .  $request->action . '%')
            ->whereIn('status_id',[4,11,18])
            ->get();
        }
        elseif($request->status ==2){
            $pending = DB::table('hospital_details_history')
            ->where('published_id', '=',Auth::user()->id)
            ->where('state_id', 'like', '%' .  $request->state_id . '%')
            ->where('action', 'like', '%' .  $request->action . '%')
            ->whereIn('status_id',[6,13,20])
            ->get();
        }
        elseif($request->status ==3){
            $pending = DB::table('hospital_details_history')
            ->where('published_id', '=',Auth::user()->id)
            ->where('state_id', 'like', '%' .  $request->state_id . '%')
            ->where('action', 'like', '%' .  $request->action . '%')
            ->whereIn('status_id',[7,14,21])
            ->get();
        }
        
        $request->flash('request',$request);
        return view('approvals.pending_publish',compact('pending'));
    }

    public function store(Request $request)
    {    
        //check if the request is publised
        if (!$this->isPublished($request->id)){            
            HospitalHistory::disableAuditing();      
            $hosp = new HospitalHistory();
            $hosp = HospitalHistory::find($request->id);
            $facility_name = $hosp['facility_name'];
            $state_id = $hosp['state_id'];
            $ward_id = $hosp['ward_id'];
            $mail_subject="";


            if($request->action == "approve"){
                if($request->requested_action == "CREATE FACILITY"){
                    $status_id = 6;
                    $message = "Facility Published";
                    $action="Create Published";
                    $mail_subject = "New Facility Created";
                    $mail_message = "New facility: '". $facility_name. "' have been created";
                }
                elseif($request->requested_action == "UPDATE FACILITY"){
                    $status_id = 13;
                    $message = "Facility Update Published";
                    $action="Update Published";
                    $mail_subject = "Facility Updated";
                    $mail_message = "Facility: '". $facility_name. "' have been updated.";
                }
                else{
                    $status_id = 20;
                    $message = "Facility Deleted";
                    $action="Delete Published";
                    $mail_subject = "Facility Deleted";
                    $mail_message = "Facility: '". $facility_name. "' have been deleted.";                
                }
            }

            if($request->action == "reject"){
                if($request->requested_action == "CREATE FACILITY"){
                    $status_id = 7;
                    $message = "Facility Publish Rejected";
                    $action="Create Publish Rejected";
                    $mail_message = "Publisher has rejected facility creation request. Please login to the system to review your request.";
                }
                elseif($request->requested_action == "UPDATE FACILITY"){
                    $status_id = 14;
                    $message = "Facility Publish Rejected";
                    $action="Update Publish Rejected";
                    $mail_message = "Publishere has rejected facility update request. Please login to the system to review your request.";
                }
                else{
                    $status_id = 21;
                    $message = "Facility Publish Rejected";
                    $action="Delete Publish Rejected";      
                    $mail_message = "Publisher has rejected facility deletion request. Please login to the system to review your request.";
                }
            }
            
            DB::beginTransaction();
            try {

                $date = Carbon::now()->format('Y-m-d H:i:s');
            
                $hosp->status_id = $status_id;
                $hosp->published_by = Auth::user()->id;
                $hosp->published_at = $date;
                $hosp->publish_note = $request->notes;
                $hosp->save();
                HospitalHistory::enableAuditing();
            
                $status = new StatusTracking;
                $status->hospital_id = $request->id;
                $status->user_id = Auth::user()->id;
                $status->status_id = $status_id;
                $status->note = $request->notes;
                $status->created_at = $date;
                $status->save();
            
                //insert new facility data to main table after published
                if($status_id == 6){
                        $hosp_history = new HospitalHistory;
                        $hosp_history = HospitalHistory::find($request->id);
                        
                        //copy data from  history to main
                        $hosp_main = new Hospital;  
                        $hosp_main -> fill($hosp_history->toArray());
                        $hosp_main -> unique_id = $hosp_history->unique_id;
                        $hosp_main -> start_date = $hosp_history->start_date;
                        $hosp_main -> status_id = $hosp_history->status_id;
                        $hosp_main -> created_by = $hosp_history->created_by;
                        $hosp_main -> operational_days =  $hosp_history->operational_days;        
                        $hosp_main -> save();
                        //copy ends

                        //get new hospital services
                        $services = DB::select("SELECT service_id FROM hs_hospital_services_history WHERE hospital_id = ". $request->id . ""); 

                        //insert services
                        if(!empty($services)){
                            foreach ($services as $service){
                                $hosp_services = new HospitalService;
                                $hosp_services->service_id = $service->service_id;
                                $hosp_services->hospital_id = $request->id; 
                                $hosp_services->save();
                            }
                        }                 
                        
                }

                //update hospital, and hospital services to main table
                if($status_id == 13){
                    $hosp_history = new HospitalHistory;
                    $hosp_history = HospitalHistory::find($request->id);

                    //copy data from  history to main
                    $hosp_main = new Hospital;  
                    $hosp_main = Hospital::find($request->id);
                    $hosp_main -> fill($hosp_history->toArray());
                    $hosp_main -> start_date = $hosp_history->start_date;
                    $hosp_main -> status_id = $hosp_history->status_id;
                    $hosp_main -> created_by = $hosp_history->created_by;
                    $hosp_main -> operational_days =  $hosp_history->operational_days;        
                    $hosp_main -> save();
                    //copy ends

                    //get new hospital services
                    $services = DB::select("SELECT service_id FROM hs_hospital_services_history WHERE hospital_id = ". $request->id . ""); 
        
                    if(!empty($services)){
                        // remove current services in main table
                        $deleted = DB::delete("delete from hs_hospital_services where hospital_id ='". $request->id ."' and id > 0");
                        
                        //add new services 
                        foreach ($services as $service){
                            $hosp_services = new HospitalService;
                            $hosp_services->service_id = $service->service_id;
                            $hosp_services->hospital_id = $request->id; 
                            $hosp_services->save();
                        }
                    }   
                
                }
                
                //Delete facility after final delete request published
                if($status_id == 20){
                    HospitalService::where('hospital_id', $request->id)->delete();
                    Hospital::destroy($request->id);
                }

                DB::commit();
            } catch (\Exception $ex) {
                    DB::rollback();
                    return response()->json(['error' => $ex->getMessage()], 500);
            }

            //****** Send Notifications *********
            if (config('hfr.notify_publication')){
                $notify = new ApprovalNotifications;
                $notify->sendPublicationNotification($mail_message,$request->action,$mail_subject,$state_id);    
            }
            
            session()->flash("alert-success", $message);

            if (config('hfr.integration_enabled')){

                    //******************************************************************************************************
                    // HFR DHIS 2 EXCHANGE
                    //******************************************************************************************************
                    //after publishing new facility, create a facility in dhis2 and send notifcation
                    $dhis = new HfrDhis;

                    if ($status_id == 6){
                        return view('dhis.store',compact('hosp','message'));
                    }
                    elseif($status_id == 13){  //after publishing facility updates, send updates to dhis2 and send notifcation
                        $data = $dhis->getDhisUpdatedValues($hosp, $request->id);
                        $id = $request->id;

                        if ($data != false){
                            return view('dhis.update',compact('data','id','message'));
                        }else{
                            return redirect()->route('publish.pending');
                        }
                    }else{  
                        //after publishing delete request,  delete facility in DHIS if it has no data or close if it has data
                        
                        return view('dhis.delete',compact('hosp','message'));

                        // $dhis->sendEmailtoDhisTeamForDeletedFacility($facility_name, $ward_id);
                        // return redirect()->route('publish.pending');
                    }

                    //******************************************************************************************************
                    // HFR DHIS 2 EXCHANGE END..
                    //******************************************************************************************************
            }else{

                if ($status_id == 13){
                    return redirect()->route('publish.pending');             
                }else{  
                    return redirect()->back();                
                }

            }
        //facility is already published
        }else{
            session()->flash("alert-success", 'The request is already published');
            return redirect()->back();
        }
        
    
    }
    
    //this method check to see if the request is arleady published
    //before trying to publish
    public function isPublished($fac_id){
        $hosp = new HospitalHistory();
        $hosp = HospitalHistory::find($fac_id);
        
        if ($hosp['status_id'] == 6 OR $hosp['status_id'] == 13 Or $hosp['status_id'] == 20){
            return true;        
        }else{
            return false;
        }

    }


 

   
    
}
