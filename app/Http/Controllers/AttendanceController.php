<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeSetting;
use App\Services\AttendancePrivacyService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendancePrivacyService $privacyService
    ) {
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view attendance'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $query = \App\Models\Attendance::query()
            ->with(['employee.user', 'employee.shift', 'shift']);

        if ($request->filled('employee')) {
            $search = $request->employee;

            $query->whereHas('employee.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        if ($request->filled('suspicious')) {
            $query->where('is_suspicious', $request->suspicious === '1' ? 1 : 0);
        }

        $attendances = $query->latest('attendance_date')
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        $shifts = \App\Models\Shift::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('attendances.index', compact('attendances', 'shifts'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('mark attendance for others'), 403);
        $employees = Employee::where('status', 'active')->get();
        $officeSetting = OfficeSetting::first();

        return view('attendances.create', compact('employees', 'officeSetting'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('mark attendance for others'), 403);
        $officeSetting = OfficeSetting::first();

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'nullable',
            'latitude' => [
                $officeSetting && $officeSetting->location_required ? 'required' : 'nullable',
                'numeric'
            ],
            'longitude' => [
                $officeSetting && $officeSetting->location_required ? 'required' : 'nullable',
                'numeric'
            ],
            'photo' => [
                $officeSetting && $officeSetting->selfie_required ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],
        ]);

        $existing = Attendance::where('employee_id', $request->employee_id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['attendance_date' => 'Attendance already marked for this date.']);
        }

        $checkIn = Carbon::parse($request->check_in);
        $status = 'present';

        if ($officeSetting && $checkIn->gt(Carbon::parse($officeSetting->late_after))) {
            $status = 'late';
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        }

        $agent = new Agent();
        $distance = null;
        $isSuspicious = false;
        $reasons = [];

        if ($officeSetting) {
            $distance = $this->privacyService->calculateDistanceInMeters(
                $request->latitude,
                $request->longitude,
                $officeSetting->office_latitude,
                $officeSetting->office_longitude
            );

            if (
                $officeSetting->location_required &&
                $this->privacyService->isOutsideAllowedRadius($distance, $officeSetting->allowed_radius_meters)
            ) {
                $isSuspicious = true;
                $reasons[] = 'Employee is outside office allowed radius';
            }

            if ($officeSetting->selfie_required && !$photoPath) {
                $isSuspicious = true;
                $reasons[] = 'Selfie proof missing';
            }
        }

        if (!$request->latitude || !$request->longitude) {
            $isSuspicious = true;
            $reasons[] = 'Location not captured';
        }

        Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $status,
            'ip_address' => $request->ip(),
            'device_name' => $agent->device(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'user_agent' => $request->userAgent(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_from_office' => $distance,
            'photo_path' => $photoPath,
            'privacy_note' => 'Attendance captured with privacy validation: IP, browser, platform, location, selfie proof.',
            'is_suspicious' => $isSuspicious,
            'suspicious_reason' => !empty($reasons) ? implode(', ', $reasons) : null,
        ]);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance marked successfully.');
    }

    public function show(Attendance $attendance)
    {
        abort_unless(auth()->user()->can('view attendances'), 403);
        $attendance->load('employee');

        return view('attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        abort_unless(auth()->user()->can('edit attendances'), 403);
        $employees = Employee::where('status', 'active')->get();

        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        abort_unless(auth()->user()->can('edit attendances'), 403);
        $request->validate([
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|in:present,absent,late,half_day',
            'admin_review_note' => 'nullable|string',
        ]);

        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'admin_review_note' => $request->admin_review_note,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        abort_unless(auth()->user()->can('delete attendances'), 403);
        $attendance->delete();

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }
}