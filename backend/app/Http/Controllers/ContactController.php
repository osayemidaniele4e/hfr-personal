<?php

namespace App\Http\Controllers;

use App\Contactus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

use App\Mail\SendFeedback;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function openContactForm()
    {
        return view('public.contact');
    }

    public function index()
    {
        $message = Contactus::paginate(10);
        return view('messages.index', compact("message"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'subject' => 'required|string|max:20',
            'email' => 'required|string|email|max:100',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $this->sendFeedbackEmail($request);

        Contactus::create($request->all());

        session()->flash("alert-success", "Thanks " . $request->name . " for your message/feedback. We will get back to you!");
        return back();
    }

    private function sendFeedbackEmail($request)
    {


        $user_emails = DB::select("SELECT u.email FROM users u
                    JOIN model_has_roles r on r.model_id = u.id
                    JOIN role_has_permissions p on p.role_id = r.role_id
                    WHERE p.permission_id = 63");

        $emails = [];
        foreach ($user_emails as $email) {
            $emails[] = $email->email;
        }

        Mail::to($emails)
            ->send(new SendFeedback($request->full_name, $request->email, $request->message, $request->subject));
    }
}
