<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UpdateRequest extends Notification implements ShouldQueue
{
    use Queueable;

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
        ->line('Facility updates have been requested. Please login to the system to review and verify the request.')
        ->action('Click Here to Login', $url);
    }
}
