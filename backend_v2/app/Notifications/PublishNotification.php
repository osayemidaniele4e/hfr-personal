<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PublishNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $message,$action,$subject;

    public function __construct($message,$action,$subject)
    {
        $this->message = $message;
        $this->action = $action;
        $this->subject = $subject;
    }
 
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
    
        if ($this->action =="approve"){

            return (new MailMessage)
                ->subject($this->subject)
               ->view('vendor.notifications.facility_approval', [
                'message' => $this->message,
            ]);
        }

        if ($this->action =="reject"){
            $url = url('admin/hospitals/approvals/validation');

            return (new MailMessage)
                ->subject('Facility Publication Rejected')
                ->view('vendor.notifications.facility_approval', [
                'actionUrl' => $url,
                'message' => $this->message,
            ]);
        }
    }


}
