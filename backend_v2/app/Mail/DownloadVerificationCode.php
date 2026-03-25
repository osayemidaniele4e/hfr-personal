<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DownloadVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $tokenUrl;

    public function __construct($code, $tokenUrl)
    {
        $this->code = $code;
        $this->tokenUrl = $tokenUrl;
    }

    public function build()
    {
        return $this->subject('HFR Verification Code')
            ->view('mails.verification')
            ->with(['code' => $this->code, 'tokenUrl' => $this->tokenUrl]);
    }
}
