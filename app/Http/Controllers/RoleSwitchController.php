<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = auth()->user();

        if (!$user->roles->pluck('name')->contains($request->role)) {
            abort(403, 'You do not have permission to switch to this role.');
        }

        session(['active_role' => $request->role]);

        if ($request->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Switched to Admin role.');
        } elseif ($request->role === 'employee') {
            return redirect()->route('profile.employee')->with('success', 'Switched to Employee role.');
        }

        return redirect()->back()->with('success', 'Switched to ' . ucfirst($request->role) . ' role.');
    }
}
