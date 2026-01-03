<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
	public function handle($request, Closure $next, $guard = null)
	{
			switch ($guard) {
				case 'admin' :
					if (Auth::guard($guard)->check()) {	
						return redirect()->route('dashboard');
					}
					break;
				case 'agents' :
					// Agents don't have login access (routes disabled)
					// Redirect to admin dashboard if somehow authenticated
					if (Auth::guard($guard)->check()) {	
						return redirect()->route('dashboard');
					}
					break;
				default:
					if (Auth::guard($guard)->check()) {
						return redirect()->route('dashboard');
					}
					break;
			}
		 return $next($request);
	}
}
