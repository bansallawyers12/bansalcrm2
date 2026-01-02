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
        $encoded_id = $request->route('encoded_id');
        
        // Safely decode the client ID with error handling for PHP 8.x
        try {
            $base64_decoded = base64_decode($encoded_id);
            if ($base64_decoded === false) {
                return redirect('/')->withErrors(['access' => 'Invalid client ID format.']);
            }
            $client_id = @convert_uudecode($base64_decoded);
            if ($client_id === false || $client_id === '') {
                return redirect('/')->withErrors(['access' => 'Invalid client ID format.']);
            }
        } catch (\Throwable $e) {
            return redirect('/')->withErrors(['access' => 'Invalid client ID format.']);
        }
        
        if (Session::get('verified_client') != $client_id) {
            return redirect('/verify-dob/' . $encoded_id)->withErrors(['access' => 'You need to verify your DOB first.']);
        }
        return $next($request);
    }
}
