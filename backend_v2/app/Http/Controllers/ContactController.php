<?php

namespace App\Http\Controllers;

use App\Models\Contactus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use App\Mail\SendFeedback;
use Illuminate\Support\Facades\Mail;


/**
 * @group Administration - Contact
 *
 * APIs for handling contact messages and feedback submissions.
 *
 * This controller allows users to submit messages via a public contact form
 * and allows admins to view submitted messages. Emails are sent to admins
 * with the feedback details.
 */
class ContactController extends Controller
{

    /**
     * Display the public contact form.
     *
     *
     * @return \Illuminate\View\View
     */
    public function openContactForm()
    {
        return view('public.contact');
    }


     /**
     * List all contact messages (admin view).
     *
     *
     * @queryParam page integer The page number for pagination. Example: 1
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $message = Contactus::paginate(10);
        return view('messages.index', compact("message"));
    }



     /**
     * Store a new contact message and send email to admins.
     *
     *
     * @bodyParam full_name string required Full name of the sender. Example: John Doe
     * @bodyParam email string required Email address of the sender. Example: john@example.com
     * @bodyParam subject string required Subject of the message. Example: Feedback
     * @bodyParam message string required Message content. Example: I love your platform.
     * @bodyParam g-recaptcha-response string required Google reCAPTCHA token.
     *
     * @response 200 {
     *   "message": "Thanks John Doe for your message/feedback. We will get back to you!"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *       "email": ["The email must be a valid email address."]
     *   }
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
