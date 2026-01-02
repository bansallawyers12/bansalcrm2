<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateMultiGuard
{
    /**
     * Handle an incoming request.
     * 
     * Accepts multiple guards and authenticates if any of them are valid.
     * Sets the authenticated guard for use in the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $authenticated = false;
        
        // Try each guard until one is authenticated
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Set this guard as the default for this request
                Auth::shouldUse($guard);
                $authenticated = true;
                
                // Set a flag so we know which guard was used
                $request->attributes->set('auth_guard', $guard);
                
                break;
            }
        }
        
        // If no guard authenticated, redirect to login
        if (!$authenticated) {
            // Try to determine which login page to redirect to based on guards
            if (in_array('admin', $guards) && in_array('agents', $guards)) {
                // If both guards, default to admin login
                return redirect()->route('admin.login');
            } elseif (in_array('admin', $guards)) {
                return redirect()->route('admin.login');
            } elseif (in_array('agents', $guards)) {
                return redirect()->route('agent.login');
            }
            
            // Default fallback
            return redirect()->route('admin.login');
        }
        
        return $next($request);
    }
}

