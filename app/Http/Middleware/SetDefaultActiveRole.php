<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetDefaultActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !session()->has('active_role')) {
            $user = auth()->user();
            // Set default active role: prefer admin, then hr, manager, sales, employee, or just the first role
            $role = $user->roles->first()?->name;
            if ($role) {
                session(['active_role' => $role]);
            }
        }
        return $next($request);
    }
}
