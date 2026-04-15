<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AllowedIp;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $currentIp = $request->ip();

        // Admin bypass if needed
        if (!$user->hasAnyRole(['admin', 'employee'])) {
            $isAllowed = AllowedIp::query()
                ->where('is_active', true)
                ->where('ip_address', $currentIp)
                ->where(function ($query) use ($user) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', $user->id);
                })
                ->exists();
        
            if (!$isAllowed) {
                Auth::logout();
        
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'You do not have permission to log in from this location. You may be outside the allowed office premises or your access may not be authorized. Please contact your manager for further information.',
                    ]);
            }
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}