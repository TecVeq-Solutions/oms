<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeePersonalDetail;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view employees'), 403);
        $query = Employee::query()->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department');

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        // abort_unless(auth()->user()->can('create employees'), 403);
        return view('employees.create');
    }
    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('create employees'), 403);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',

            // personal
            'father_name' => 'nullable|string|max:255',
            'cnic_number' => 'nullable|string|max:25|unique:employee_personal_details,cnic_number',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',

            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',

            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:255',

            // files
            'profile_photo' => 'nullable|image|max:2048',
            'cnic_front_photo' => 'nullable|file|max:4096',
            'cnic_back_photo' => 'nullable|file|max:4096',
            'document_1' => 'nullable|file|max:4096',
            'document_2' => 'nullable|file|max:4096',
            'document_3' => 'nullable|file|max:4096',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('employee');

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $this->generateEmployeeCode(),
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'designation' => $request->designation,
                'joining_date' => $request->joining_date,
                'status' => $request->status,
            ]);

            // upload files
            $profilePhoto = $request->file('profile_photo')?->store('employee/profile', 'public');
            $cnicFront = $request->file('cnic_front_photo')?->store('employee/cnic', 'public');
            $cnicBack = $request->file('cnic_back_photo')?->store('employee/cnic', 'public');
            $doc1 = $request->file('document_1')?->store('employee/docs', 'public');
            $doc2 = $request->file('document_2')?->store('employee/docs', 'public');
            $doc3 = $request->file('document_3')?->store('employee/docs', 'public');

            EmployeePersonalDetail::create([
                'employee_id' => $employee->id,
                'father_name' => $request->father_name,
                'cnic_number' => $request->cnic_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'current_address' => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'city' => $request->city,
                'country' => $request->country,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'profile_photo' => $profilePhoto,
                'cnic_front_photo' => $cnicFront,
                'cnic_back_photo' => $cnicBack,
                'document_1' => $doc1,
                'document_2' => $doc2,
                'document_3' => $doc3,
            ]);

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Employee $employee)
    {
        abort_unless(auth()->user()->can('view employees'), 403);

        $employee->load([
            'user',
            'attendances',
            'personalDetail'
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        abort_unless(auth()->user()->can('edit employees'), 403);

        $employee->load('user');

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        abort_unless(auth()->user()->can('edit employees'), 403);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|string|min:8|confirmed',

            'cnic_number' => 'nullable|string|max:25|unique:employee_personal_details,cnic_number,' . optional($employee->personalDetail)->id,
        ]);

        DB::beginTransaction();

        try {

            // update user
            $employee->user->update([
                'name' => $request->full_name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $employee->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // update employee
            $employee->update($request->only([
                'full_name',
                'email',
                'phone',
                'department',
                'designation',
                'joining_date',
                'status'
            ]));

            $personal = $employee->personalDetail ?? new EmployeePersonalDetail([
                'employee_id' => $employee->id
            ]);

            // file replace helper
            function replaceFile($request, $field, $oldPath)
            {
                if ($request->hasFile($field)) {
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    return $request->file($field)->store('employee/docs', 'public');
                }
                return $oldPath;
            }

            $personal->profile_photo = replaceFile($request, 'profile_photo', $personal->profile_photo);
            $personal->cnic_front_photo = replaceFile($request, 'cnic_front_photo', $personal->cnic_front_photo);
            $personal->cnic_back_photo = replaceFile($request, 'cnic_back_photo', $personal->cnic_back_photo);
            $personal->document_1 = replaceFile($request, 'document_1', $personal->document_1);
            $personal->document_2 = replaceFile($request, 'document_2', $personal->document_2);
            $personal->document_3 = replaceFile($request, 'document_3', $personal->document_3);

            $personal->fill($request->only([
                'father_name',
                'cnic_number',
                'date_of_birth',
                'gender',
                'current_address',
                'permanent_address',
                'city',
                'country',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
            ]));

            $personal->employee_id = $employee->id;
            $personal->save();

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function destroy(Employee $employee)
    {
        abort_unless(auth()->user()->can('delete employees'), 403);
        $user = $employee->user;

        $employee->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    protected function generateEmployeeCode(): string
    {
        $last = \App\Models\Employee::orderByDesc('id')->first();

        $next = 1;

        if ($last && preg_match('/TecVeq-EMP-(\d+)/', $last->employee_code, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'TecVeq-EMP-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}