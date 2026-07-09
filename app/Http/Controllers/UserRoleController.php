<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = User::query()->with(['roles', 'employee.shift']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('user-roles.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user->load('employee.shift');

        $roles = Role::orderBy('name')->get();
        $shifts = Shift::where('is_active', true)->orderBy('name')->get();

        return view('user-roles.edit', compact('user', 'roles', 'shifts'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        if (auth()->id() === $user->id && !in_array('admin', $request->roles)) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->syncRoles($request->roles);

        if ($user->employee) {
            $user->employee->update([
                'shift_id' => $request->shift_id,
            ]);
        }

        return redirect()
            ->route('user-roles.index')
            ->with('success', 'User role and shift updated successfully.');
    }
}