<?php

namespace App\Http\Middleware;

use Closure;

class PasswordNotChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        //if user has not changed password, direct to change pasword form
        if ($user->status == -1) {
            return redirect()->route('newuser.PasswordForm');
        }

        return $next($request);
    }
}
