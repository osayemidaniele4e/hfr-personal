<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendDownloadVerificationCode extends Notification implements ShouldQueue
{
    use Queueable;

 
    private $code;
    
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

 
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('HFR Verification Code')
                ->greeting('Hello,')
                ->line('Please use the following code to complete verification:')
                ->line($this->code)
                ->line('This code will expire in 15 minutes.');
    }

 
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
