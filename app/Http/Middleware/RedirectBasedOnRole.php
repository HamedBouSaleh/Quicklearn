<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $role = strtolower($request->user()->role);
            
            // If user is accessing /dashboard, redirect to role-specific dashboard
            if ($request->is('dashboard')) {
                switch ($role) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'instructor':
                        return redirect()->route('instructor.dashboard');
                    case 'student':
                        return redirect()->route('student.dashboard');
                }
            }
        }
        
        return $next($request);
    }
}
