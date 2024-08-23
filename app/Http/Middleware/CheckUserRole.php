<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade for user authentication
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and has admin or lecturer role
        if (Auth::check() && Auth::user()->hasRole(['admin', 'lecturer'])) {
            return $next($request);
        }

        // Redirect or abort if user does not have required roles
        return redirect()->route('main')->with('error', 'Unauthorized access.');
    }
}
