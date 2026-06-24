<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee;

class TrackingApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Tracking-Token');

        if (!$token) {
            return response()->json(['error' => 'Tracking token missing'], 401);
        }

        $employee = Employee::where('tracking_api_token', $token)
            ->where('is_tracked', true)
            ->where('status', 'active')
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Invalid or inactive tracking token'], 401);
        }

        $employee->update(['last_tracking_heartbeat' => now()]);

        $request->merge(['tracked_employee' => $employee]);

        return $next($request);
    }
}
