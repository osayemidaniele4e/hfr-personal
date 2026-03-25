<?php

namespace App\Mail;

use App\Models\ApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiKeyApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $plainKey;

    public function __construct(ApiClient $client, string $plainKey)
    {
        $this->client = $client;
        $this->plainKey = $plainKey;
    }

    public function build()
    {
        return $this->subject('Your HFR API Key Has Been Approved')
            ->view('mails.api-key-approved')
            ->with([
                'client' => $this->client,
                'plainKey' => $this->plainKey,
            ]);
    }
}
