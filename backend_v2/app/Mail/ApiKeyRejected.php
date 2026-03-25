<?php

namespace App\Mail;

use App\Models\ApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiKeyRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('Your HFR API Key Request Update')
            ->view('mails.api-key-rejected')
            ->with([
                'client' => $this->client,
            ]);
    }
}
