<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CreateRequest;
use App\Notifications\DeleteRequest;
use App\Notifications\UpdateRequest;
use App\Notifications\VerificationNotification;
use App\Notifications\ValidationNotification;
use App\Notifications\PublishNotification;
// use App\User;

class ApprovalNotifications extends Model
{
    public function sendFacilityCreateRequestNotification($state_id)
    {
        $users = User::role(7)->where('state_id', $state_id)->get();
        Notification::send($users, new CreateRequest());
    }

    public function sendFacilityUpdateRequestNotification($state_id)
    {
        $users = User::role(7)->where('state_id', $state_id)->get();
        Notification::send($users, new UpdateRequest());
    }

    public function sendFacilityDeleteRequestNotification($state_id)
    {
        $users = User::role(7)->where('state_id', $state_id)->get();
        Notification::send($users, new DeleteRequest());
    }

    public function sendVerificationNotification($message, $action, $requested_by)
    {
        if ($action === "approve") {
            $users = User::role(6)->where('state_id', Auth::user()->state_id)->get();
            Notification::send($users, new VerificationNotification($message, $action));
        }

        if ($action === "reject") {
            $user = User::find($requested_by);
            Notification::send($user, new VerificationNotification($message, $action));
        }
    }

    public function sendValidationNotification($message, $action)
    {
        if ($action === "approve") {
            $users = User::role(5)->where('state_id', Auth::user()->state_id)->get();
            Notification::send($users, new ValidationNotification($message, $action));
        }

        if ($action === "reject") {
            $users = User::role(7)->where('state_id', Auth::user()->state_id)->get();
            Notification::send($users, new ValidationNotification($message, $action));
        }
    }

    public function sendPublicationNotification($message, $action, $subject, $state_id)
    {
        if ($action === "approve") {
            $users = User::where('state_id', $state_id)->get();
            Notification::send($users, new PublishNotification($message, $action, $subject));
        }

        if ($action === "reject") {
            $users = User::role(6)->where('state_id', $state_id)->get();
            Notification::send($users, new PublishNotification($message, $action, $subject));
        }
    }
}
