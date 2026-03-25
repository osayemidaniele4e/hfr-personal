<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class sendDeleteFacilityEmailtoDhisTeam extends Notification implements ShouldQueue
{
    use Queueable;
 
    private $name,$state,$lga,$ward;

    public function __construct($name,$state,$lga,$ward)
    {
        $this->name = $name;
        $this->state = $state;
        $this->lga = $lga;
        $this->ward = $ward;
    }
   
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Facility Deleted')
        ->view('vendor.notifications.facility_deleted', [
            'name' => $this->name,
            'state' => $this->state,
            'lga' => $this->lga,
            'ward' => $this->ward
        ]);
}

 
}
