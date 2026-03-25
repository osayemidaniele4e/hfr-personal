<?php

namespace App\Http\Controllers\Approval;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function tracking(Request $request)
    {
        $pending = DB::table('hospital_details_history')
            ->whereIn('status_id',[1,8,15,5,12,19])           
            ->orderby('updated_at','desc')
            ->get();
        
        $message=$pending->count()." pending verifications";    
        
        return view('approvals.approval_tracking',compact('pending','message'));
    }

    public function tracking_search(Request $request)
    {
        if ($request->approval ==1){
            $pending = DB::table('hospital_details_history')
                ->where('state_id', 'like', '%' .  $request->state_id . '%')
                ->where('action', 'like', '%' .  $request->action . '%')
                ->whereIn('status_id',[1,8,15,5,12,19])           
                ->orderby('updated_at','desc')
                ->get();

            $message=$pending->count()." pending verifications";    
        }
        elseif($request->approval ==2){
            $pending  = DB::table('hospital_details_history')
                ->where('state_id', 'like', '%' .  $request->state_id . '%')
                ->where('action', 'like', '%' .  $request->action . '%')
                ->whereIn('status_id',[2,7,9,14,16,21])
                ->get();
            
            $message=$pending->count()." pending validations";  
        }
        else{
            $pending = DB::table('hospital_details_history')
                ->where('state_id', 'like', '%' .  $request->state_id . '%')
                ->where('action', 'like', '%' .  $request->action . '%')
                ->whereIn('status_id',[4,11,18])
                ->get();

            $message=$pending->count()." pending publications";  
        }

        $request->flash('request',$request);
        return view('approvals.approval_tracking',compact('pending','message'));

    }
}
