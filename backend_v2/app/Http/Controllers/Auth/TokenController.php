<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


/**
 * @group Authentication
 *
 * Token-based Login Flow
 *
 * These endpoints handle the second step of a two-factor login flow.
 * After submitting username/password, the system sends a token to the user
 * and stores temporary login details in the session. The user must validate
 * the token here before being logged in.
 */
class TokenController extends Controller
{

     /**
     * Display the token verification page.
     *
     * This endpoint shows the token entry screen to the user, but only if a
     * token has been generated and stored in the session during the login flow.
     *
     * If no token exists in the current session, the user is redirected to `/`.
     *
     * @responseField view string The HTML of the token verification page.
     */
    public function getToken(Request $request){
        if (!$request->session()->has('token')){
            return redirect()->to('/');
        }

        return view('auth.token');
    }


     /**
     * Verify the submitted token and complete login.
     *
     * Validates the token submitted by the user against the token stored in the session.
     * If the token has expired, or does not match, the user is redirected back with an error.
     *
     * Upon successful validation, the system logs the user in using the stored user ID
     * and clears the token session data, then redirects to `/administrator`.
     *
     * @bodyParam token string required The token sent to the user.
     *
     * @response 302 {
     *   "redirect": "/administrator"
     * }
     *
     * @response 302 {
     *   "error": "Invalid token or token expired"
     * }
     */
    public function postToken(Request $request){

        if ($request->session()->get('token.expire_at') < Carbon::now() ){
            return redirect()->back()->withErrors([
                'token' => 'Token has expired',
            ]);
        }

        if ($request->token != $request->session()->get('token.token')){
            return redirect()->back()->withErrors([
                'token' => 'Invalid token',
            ]);
        }

        if (Auth::loginUsingId($request->session()->get('token.user_id'),$request->session()->get('token.remember'))){
            $request->session()->forget('token');
            return redirect('/administrator');

        }else{
            return redirect()->url('/');
        }

    }

}
