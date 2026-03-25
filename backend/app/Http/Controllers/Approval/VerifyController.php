<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\StatusTracking;
use Auth;
use Carbon\Carbon;
use App\HospitalHistory;
use App\HospitalServiceHistory;
use App\audit;
use App\ApprovalNotifications;

class VerifyController extends Controller
{
    public function index()
    {
      
        if(auth()->user()->hasAnyPermission([1000])){
            $pending = DB::table('hospital_details_history')
            ->where('state_id', '=',Auth::user()->state_id)
            ->whereIn('status_id',[1,8,15,5,12,19])           
            ->orderby('updated_at','desc')
            ->get();
        }else{
            $pending = DB::table('hospital_details_history')
            ->where('state_id', '=',Auth::user()->state_id)
            ->whereIn('lga_id', auth()->user()->getDirectPermissions()->pluck('id')->toArray())
            ->whereIn('status_id',[1,8,15,5,12,19])           
            ->orderby('updated_at','desc')
            ->get();
        }
          
     
        return view('approvals.pending_verify',compact('pending')); 
    }

    public function search(Request $request)
    {
        if ($request->status ==1){
            if(auth()->user()->hasAnyPermission([1000])){
                $pending = DB::table('hospital_details_history')
                ->where('state_id', '=',Auth::user()->state_id)
                ->where('action', 'like', '%' .  $request->action . '%')
                ->whereIn('status_id',[1,8,15,5,12,19])           
                ->orderby('updated_at','desc')
                ->get();
            }else{
                $pending = DB::table('hospital_details_history')
                ->where('state_id', '=',Auth::user()->state_id)
                ->where('action', 'like', '%' .  $request->action . '%')
                ->whereIn('lga_id', auth()->user()->getDirectPermissions()->pluck('id')->toArray())
                ->whereIn('status_id',[1,8,15,5,12,19])           
                ->orderby('updated_at','desc')
                ->get();
            }
        }
        elseif($request->status ==2){
            $pending = DB::table('hospital_details_history')
            ->where('verified_id', '=',Auth::user()->id)
            ->where('action', 'like', '%' .  $request->action . '%')
            ->whereIn('status_id',[2,9,16])
            ->orderby('updated_at','desc')
            ->get();
        }
        elseif($request->status ==3){
            $pending = DB::table('hospital_details_history')
            ->where('verified_id', '=',Auth::user()->id)
            ->where('action', 'like', '%' .  $request->action . '%')
            ->whereIn('status_id',[3,10,17])
            ->orderby('updated_at','desc')
            ->get();
        }
        else{
            $pending = DB::table('hospital_details_history')
            ->where('verified_id', '=',Auth::user()->id)
            ->where('action', 'like', '%' .  $request->action . '%')
            ->orderby('updated_at','desc')
            ->get();
        }
      
        $request->flash('request',$request);     
        return view('approvals.pending_verify',compact('pending')); 
    }

    public function store(Request $request)
    {
     
        HospitalHistory::disableAuditing();       
        $hosp = new HospitalHistory;
        $hosp = HospitalHistory::findOrFail($request->id);

        if($request->action == "approve"){
            if($request->requested_action == "CREATE FACILITY"){
                $status_id = 2;
                $action="Create Verified";
                $message = "Facility Creation Verified";
                $mail_message = "Facility creation request has been verified. Please login to the system to review and validate the request.";
            }
            elseif($request->requested_action == "UPDATE FACILITY"){
                $status_id = 9;
                $action="Update Verified";
                $message = "Facility Update Verified";
                $mail_message = "Facility update request has been verified. Please login to the system to review and validate the request.";
            }
            else{
                $status_id = 16;
                $action="Delete Verified";
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

        if($request->action == "reject"){
            if($request->requested_action == "CREATE FACILITY"){
                $status_id = 3;
                $action="Create Verification Rejected";
                $message = "Facility Creation Rejected";
                $mail_message = "Verifier has rejected facility creation request. Please login to the system to review your request.";
            }
            elseif($request->requested_action == "UPDATE FACILITY"){
                $status_id = 10;
                $action="Update Verification Rejected";
                $message = "Facility Update Rejected";
                $mail_message = "Verifier has rejected facility update request. Please login to the system to review your request.";
            }
            else{
                $status_id = 17;
                $action="Delete Verification Rejected";
                $message = "Facility Deletion Rejected";
                $mail_message = "Verifier has rejected facility deletion request. Please login to the system to review your request.";
            }
        }        

        $date = Carbon::now()->format('Y-m-d H:i:s');

        $hosp->status_id = $status_id;
        $hosp->verified_by = Auth::user()->id;
        $hosp->verified_at = $date;
        $hosp->verify_note = $request->notes;
    
        $status = new StatusTracking;
        $status->hospital_id = $request->id;
        $status->user_id = Auth::user()->id;
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

        HospitalHistory::enableAuditing();

        //send notifications
        if(config('hfr.notify_validator')){
            $notify = new ApprovalNotifications;
            $notify->sendVerificationNotification($mail_message,$request->action,$hosp->requested_by);
        }
 
     
        session()->flash("alert-success", $message);

        
        if ($status_id == 9 OR $status_id == 10){
            return redirect()->route('verify.pending');
        }else{  
            return redirect()->back();                
        }
    }

    public function recall(Request $request)
    {
        if($this->isVerified($request->hosp_id)){
            if($request->action == "CREATE FACILITY"){
                $status_id = 1;
                $action="Recall Create Verification";
            }
            elseif($request->action == "UPDATE FACILITY"){
                $status_id = 8;
                $action="Recall Update Verification";
            }
            else{
                $status_id = 15;
                $action="Recall Delete Verification";
            }
    
        
            $date = Carbon::now()->format('Y-m-d H:i:s');
    
            HospitalHistory::disableAuditing();       
            $hosp = new HospitalHistory;
            $hosp = HospitalHistory::findOrFail($request->hosp_id);
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
    
            HospitalHistory::enableAuditing();
    
            session()->flash("alert-success", "Verification recalled successfully!");
        }
        else{
            session()->flash("alert-success", "Can not recall validated or published request!");
        }

        return redirect()->route('verify.pending');
       
    }

    private function isVerified($id){
        $hosp = HospitalHistory::find($id);
       
        if (in_array($hosp->status_id,[2,9,16])){
            return true;
        }else{
            return false;
        }
    }

}
