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
            ->greeting('Hello,')
            ->line('Facility ('. $this->name .') have been deleted in DHIS2. The facility was in '.$this->state. ' state, '.$this->lga. ' LGA, and '.$this->ward. ' ward.');
    }

 
}
