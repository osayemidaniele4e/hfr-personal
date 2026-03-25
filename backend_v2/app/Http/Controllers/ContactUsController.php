<?php

namespace App\Http\Controllers;

use App\Models\Contactus;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        // \Log::info($request);
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'captchaToken' => 'required',
        ]);

        // \Log::info($validated);

        // Check if the reCAPTCHA token is present
        if (!$request->input('captchaToken')) {
            return response()->json([
                'message' => 'Please complete the reCAPTCHA to proceed.'
            ], 422);
        }

        // Initialize Guzzle client
        $client = new Client();
        // \Log::info('Before reCAPTCHA verification');
        // Verify the reCAPTCHA token with Google
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'   => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('captchaToken'), // Use 'captchaToken' here
            ]
        ]);

        // Decode the response body
        $body = json_decode((string) $response->getBody(), true);

        // \Log::info('reCAPTCHA Response', ['response' => $body]);

        // Check if the reCAPTCHA verification was successful and score is above the threshold
        if (!isset($body['success']) || !$body['success']) {
            return response()->json(['message' => 'reCAPTCHA verification failed.'], 422);
        }

        // Save to database
        $contact = Contactus::create($request->all());

        // Send mail to admin
        try {
            Mail::to(env("CONTACT_US_MAIL"))->send(new ContactMail($contact));
            // \Log::info('Mail sent successfully');
        } catch (\Exception $e) {
            \Log::error('Error sending email', ['error' => $e->getMessage()]);
        }

        // Mail::to(env("CONTACT_US_MAIL"))->send(new ContactMail($contact));

        // return response()->json(['success' => "Thanks " . $request->full_name . " for your message."], 200);
        return response()->json(['success' => "Thanks " . ucwords(strtolower($request->full_name))  . " for your message."], 200);
    }
}
