<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmailNewUser extends Notification implements ShouldQueue
{
    use Queueable;

    private $name,$password,$email;

    
    public function __construct($name,$password,$email)
    {
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
    }
 

    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        $url = url('/login');

        return (new MailMessage)
                ->subject('HFR User Account')
                ->greeting('Dear '. $this->name)
                ->line('Your HFR account registration is complete')
                ->line('Please login using your email: '. $this->email .'  and temporary password: '. $this->password)
                ->action('Login', $url)
                ->line('You will be required to change your password before you proceed!');

    }

 
}
