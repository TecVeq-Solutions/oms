<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WolfinAppAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized. Token not provided.'], 401);
        }

        $appToken = \App\Models\WolfinAppToken::where('token', $token)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })->first();

        if (!$appToken) {
            return response()->json(['error' => 'Unauthorized. Invalid or expired token.'], 401);
        }

        // Add the device_id to the request so we know who is calling
        $request->merge(['wolfin_device_id' => $appToken->device_id]);

        return $next($request);
    }
}
