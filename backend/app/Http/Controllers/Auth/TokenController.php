<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function getToken(Request $request){
        if (!$request->session()->has('token')){
            return redirect()->to('/');
        }

        return view('auth.token');
    }

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
