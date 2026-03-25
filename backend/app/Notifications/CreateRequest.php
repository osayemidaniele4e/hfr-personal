<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CreateRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * this notification will be sent to verifiers after
     * a new facility request have been submitted
     */

    
    public function __construct()
    {
    }
 

    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        $url = url('admin/hospitals/approvals/verify');

        return (new MailMessage)
        ->subject('Facility Verification Request')
        ->greeting('Hello,')
        ->line('New facility have been created. Please login to the system to review and verify the request.')
        ->action('Click Here to Login', $url);
    }
 
  
}
