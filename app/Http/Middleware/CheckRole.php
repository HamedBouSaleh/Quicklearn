<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $userRole = strtolower($request->user()->role);
        
        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return $next($request);
            }
        }

        // Redirect based on user's actual role
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden. You do not have permission to access this resource.'], 403);
        }
        
        return redirect()->route($userRole . '.dashboard')
            ->with('error', 'You do not have permission to access that page.');
    }
}
