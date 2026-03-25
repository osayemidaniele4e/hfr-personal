<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $full_name,$email,$feedback_message,$subject;

    public function __construct($full_name,$email,$feedback_message,$subject)
    {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->feedback_message = $feedback_message;
        $this->subject = $subject;
    }


    public function build()
    {
        return $this->subject('HFR User Feedback: '. $this->subject)
                    ->view('mails.sendfeedback');
              
    }
}
