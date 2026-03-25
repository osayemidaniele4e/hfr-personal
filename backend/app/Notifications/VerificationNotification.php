<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationNotification extends Notification implements ShouldQueue
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
        if ($this->action =="approve"){
            $url = url('admin/hospitals/approvals/validation');

            return (new MailMessage)
            ->subject('Facility Validation Requested')
            ->greeting('Hello,')
            ->line($this->message)
            ->action('Click Here to Login', $url);
        }

        if ($this->action =="reject"){
            $url = url('admin/hospitals/myrequest/pending');

            return (new MailMessage)
            ->subject('Facility Verification Rejected')
            ->greeting('Hello,')
            ->line($this->message)
            ->action('Click Here to Login', $url);
        }
    

    }

}
