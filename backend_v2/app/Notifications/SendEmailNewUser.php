<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmailNewUser extends Notification implements ShouldQueue
{
    use Queueable;

    private $name, $password, $email;


    public function __construct($name, $password, $email)
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
            ->view('mails.custom_new_user', [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'loginUrl' => url('/login'),
            ]);
    }
}
