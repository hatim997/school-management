<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountActivation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exclude specific routes from redirection
    if (!$request->user() || $request->routeIs(['login', 'login.attempt', 'register', 'register.attempt', 'logout', 'deactivated', 'admin-approval'])) {
        return $next($request);
    }

        // Check authentication
        if (Auth::check()) {
            $status = Auth::user()->is_active;

            if ($status === 'active') {
                return $next($request);
            }

            if ($status === 'inactive') {
                return redirect()->route('deactivated');
            }

            if ($status === 'pending') {
                return redirect()->route('admin-approval');
            }
        }

        // If user not authenticated, redirect to login
        return redirect()->route('login');
    }
}
