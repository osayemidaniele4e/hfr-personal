<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class sendUpdateFacilityEmailtoDhisTeam extends Notification implements ShouldQueue
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
        $url = url('admin/hfr-dhis-exchange/logs');

        return (new MailMessage)
            ->subject('Facility Updated')
            ->greeting('Hello,')
            ->line('Facility ('. $this->name .') have been updated in DHIS2. The facility is located in '.$this->state. ' state, '.$this->lga. ' LGA, and '.$this->ward. ' ward.')
            ->line('Please check exchange logs in HFR for more details.')
            ->action('View Logs', $url)
            ->line('Kindly take appropriate actions at your end!');
    }

  
}
