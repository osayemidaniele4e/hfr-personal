<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteRequest extends Notification implements ShouldQueue
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

     /*    return (new MailMessage)
        ->subject('Facility Verification Request')
        ->greeting('Hello,')
        ->line('Facility deletion have been requested. Please login to the system to review and verify the request.')
        ->action('Click Here to Login', $url); */

            return (new MailMessage)
            ->subject('Facility Delete Verification Request')
            ->view('vendor.notifications.facility_delete_request', [
                'actionUrl' => $url,
                'message' => 'Facility deletion have been requested. Please login to the system to review and verify the request.',
            ]);


    }
 
}
