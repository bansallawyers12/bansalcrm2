<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckDobSession
{
    public function handle(Request $request, Closure $next)
    {
        $client_id = convert_uudecode(base64_decode($request->route('encoded_id')));
        if (Session::get('verified_client') != $client_id) {
            return redirect('/verify-dob/' . $request->route('encoded_id'))->withErrors(['access' => 'You need to verify your DOB first.']);
        }
        return $next($request);
    }
}
