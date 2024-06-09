<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckIsConnected
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_connected == false) {
            Auth::logout();
            return redirect('/login')->with('error', 'Votre session a expiré.');
        }

        return $next($request);
    }
}
