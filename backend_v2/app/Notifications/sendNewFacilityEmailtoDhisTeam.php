<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class sendNewFacilityEmailtoDhisTeam extends Notification implements ShouldQueue
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
    $actionUrl = url('admin/hfr-dhis-exchange/logs');

    return (new MailMessage)
        ->subject('New Facility Created')
        ->view('vendor.notifications.new_facility_created', [
            'name' => $this->name,
            'state' => $this->state,
            'lga' => $this->lga,
            'ward' => $this->ward,
            'actionUrl' => $actionUrl
        ]);
}

 
}
