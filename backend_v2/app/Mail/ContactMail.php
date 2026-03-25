<?php

namespace App\Mail;

use App\Models\Contactus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contactus $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        // return $this->subject('HFR User Feedback: . $this->subject Contact Message')
        return $this->subject('HFR User Feedback')
            ->view('mails.contact')
            ->with(['contact' => $this->contact]);
    }
}
