<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ValidationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $message,$action;

    public function __construct($message,$action)
    {
        $this->message = $message;
        $this->action = $action;
    }
 

    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        //if apporved send notification to publisher
        if ($this->action =="approve"){
            $url = url('admin/hospitals/approvals/publish');

            return (new MailMessage)
            ->subject('Facility Publication Requested')
            ->view('vendor.notifications.facility_approval', [
                'actionUrl' => $url,
                'message' => $this->message,
            ]);
            //->greeting('Hello,')
            //->line($this->message)
            //->action('Click Here to Login', $url);
        }

        if ($this->action =="reject"){
            $url = url('admin/hospitals/approvals/verify');

            return (new MailMessage)
            ->subject('Facility Validation Rejected')
            ->view('vendor.notifications.facility_approval', [
                'actionUrl' => $url,
                'message' => $this->message,
            ]);
        }
    

    }
}
