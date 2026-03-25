<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Notification;
use Auth;
use App\User;
use App\Notifications\CreateRequest;
use App\Notifications\DeleteRequest;
use App\Notifications\UpdateRequest;
use App\Notifications\VerificationNotification;
use App\Notifications\ValidationNotification;
use App\Notifications\PublishNotification;

class ApprovalNotifications extends Model
{
    public function sendFacilityCreateRequestNotification($state_id){
        //get users with verification role
        $users = User::role(7)->where('state_id',$state_id)->get();
        Notification::send($users, new CreateRequest());
    }

    public function sendFacilityUpdateRequestNotification($state_id){
        //get users with verification role
        $users = User::role(7)->where('state_id',$state_id)->get();
        Notification::send($users, new UpdateRequest());
    }

    public function sendFacilityDeleteRequestNotification($state_id){
        //get users with verification role
        $users = User::role(7)->where('state_id',$state_id)->get();
        Notification::send($users, new DeleteRequest());
    }

    public function sendVerificationNotification($message,$action,$requested_by){

        if($action == "approve"){
            //get users with validation role
            $users = User::role(6)->where('state_id',Auth::user()->state_id)->get();
            Notification::send($users, new VerificationNotification($message,$action));
        }

        if($action == "reject"){
            //get user who submitted the request
            $users = User::find($requested_by);
            Notification::send($users, new VerificationNotification($message,$action));
        }
    }

    public function sendValidationNotification($message,$action){

        if($action == "approve"){
            //get users with publish role
            $users = User::role(5)->where('state_id',Auth::user()->state_id)->get();
            Notification::send($users, new ValidationNotification($message,$action));
        }

        if($action == "reject"){
            //get user who verification role and send rejection notification
            $users = User::role(7)->where('state_id',Auth::user()->state_id)->get();
            Notification::send($users, new ValidationNotification($message,$action));
        }
    }

    public function sendPublicationNotification($message,$action,$subject,$state_id){

        if($action == "approve"){
            $users = User::where('state_id',$state_id)->get();
            Notification::send($users, new PublishNotification($message,$action,$subject));
        }

        if($action == "reject"){
            //get users with validatoin role and send rejection note
            $users = User::role(6)->where('state_id',$state_id)->get();
            Notification::send($users, new PublishNotification($message,$action,$subject));
        }
    }

    

}
