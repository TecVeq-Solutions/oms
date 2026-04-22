<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\OfficeSetting;
use App\Services\AttendancePrivacyService;
use App\Services\AppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct(
        protected AttendancePrivacyService $privacyService,
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function employeeProfile()
    {
        abort_unless(auth()->user()->hasRole('employee'), 403);

        $user = auth()->user();
        $employee = $user->employee;

        return view('profile.employee', compact('employee'));
    }

    public function myAttendance()
    {
        abort_unless(auth()->user()->hasRole('employee'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $user = auth()->user();
        $employee = $user->employee;

        $attendances = $employee
            ? $employee->attendances()
                ->with('shift')
                ->latest('attendance_date')
                ->paginate(10)
            : collect();

        return view('profile.my-attendance', compact('employee', 'attendances'));
    }

    /**
     * Resolve attendance date according to assigned shift.
     * For overnight shifts, if current time falls in the carry-forward window
     * (previous day's shift start to today's shift end), attendance belongs to previous day.
     */
    private function resolveAttendanceDateForShift($shift, Carbon $currentDateTime): string
    {
        $attendanceDate = $currentDateTime->copy()->toDateString();

        if (!$shift) {
            return $attendanceDate;
        }

        if (!$shift->is_overnight) {
            return $attendanceDate;
        }

        $timezone = 'Asia/Karachi';
        $today = $currentDateTime->copy()->startOfDay();

        $previousShiftStart = Carbon::parse(
            $today->copy()->subDay()->toDateString() . ' ' . $shift->start_time,
            $timezone
        );

        $previousShiftEnd = Carbon::parse(
            $today->toDateString() . ' ' . $shift->end_time,
            $timezone
        );

        if ($previousShiftEnd->lte($previousShiftStart)) {
            $previousShiftEnd->addDay();
        }

        if ($currentDateTime->betweenIncluded($previousShiftStart, $previousShiftEnd)) {
            return $currentDateTime->copy()->subDay()->toDateString();
        }

        return $attendanceDate;
    }

    /**
     * Get latest open attendance for employee's assigned shift.
     * Open attendance always takes priority for checkout / duplicate checkin prevention.
     */
    private function getOpenShiftAttendance($employee): ?Attendance
    {
        if (!$employee || !$employee->shift) {
            return null;
        }

        return Attendance::where('employee_id', $employee->id)
            ->where('shift_id', $employee->shift->id)
            ->whereNull('check_out')
            ->latest('attendance_date')
            ->latest('id')
            ->first();
    }

    /**
     * Find relevant attendance for current shift.
     * Priority:
     * 1. Any open attendance for same employee + same shift
     * 2. Fallback to resolved attendance_date for same employee + same shift
     */
    private function getCurrentShiftAttendance($employee, Carbon $currentDateTime): ?Attendance
    {
        if (!$employee || !$employee->shift) {
            return null;
        }

        $shift = $employee->shift;

        $openAttendance = $this->getOpenShiftAttendance($employee);
        if ($openAttendance) {
            return $openAttendance;
        }

        $attendanceDate = $this->resolveAttendanceDateForShift($shift, $currentDateTime);

        return Attendance::where('employee_id', $employee->id)
            ->where('shift_id', $shift->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->latest('id')
            ->first();
    }

    /**
     * Prevent duplicate or invalid check-in based on current shift state.
     */
    private function canCheckInNow($employee, Carbon $currentDateTime): array
    {
        if (!$employee || !$employee->shift) {
            return [
                'allowed' => false,
                'message' => 'No shift assigned to your profile. Please contact admin.',
            ];
        }

        $openAttendance = $this->getOpenShiftAttendance($employee);
        if ($openAttendance) {
            return [
                'allowed' => false,
                'message' => 'You have already checked in for your current shift. Please check out first.',
            ];
        }

        $attendanceDate = $this->resolveAttendanceDateForShift($employee->shift, $currentDateTime);

        $completedAttendance = Attendance::where('employee_id', $employee->id)
            ->where('shift_id', $employee->shift->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->whereNotNull('check_out')
            ->latest('id')
            ->first();

        if ($completedAttendance) {
            return [
                'allowed' => false,
                'message' => 'Attendance already completed for your current shift.',
            ];
        }

        return [
            'allowed' => true,
            'message' => null,
        ];
    }

    public function checkInForm()
    {
        abort_unless(auth()->user()->can('mark self attendance'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $user = auth()->user();
        $employee = $user->employee?->load('shift');

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your employee profile is not linked yet.');
        }

        $now = Carbon::now('Asia/Karachi');
        $todayAttendance = $this->getCurrentShiftAttendance($employee, $now);
        $officeSetting = OfficeSetting::first();

        return view('profile.check-in', compact('employee', 'todayAttendance', 'officeSetting'));
    }

    public function storeCheckIn(Request $request)
    {
        abort_unless(auth()->user()->can('mark self attendance'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $user = auth()->user();
        $employee = $user->employee?->load('shift');

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your employee profile is not linked yet.');
        }

        if ($employee->status !== 'active') {
            return back()->with('error', 'Only active employees can mark attendance.');
        }

        if (!$employee->shift) {
            return back()->with('error', 'No shift assigned to your profile. Please contact admin.');
        }

        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'capture_source' => ['required', 'in:camera'],
        ]);

        $checkIn = Carbon::now('Asia/Karachi');
        $shift = $employee->shift;
        $attendanceDate = $this->resolveAttendanceDateForShift($shift, $checkIn);

        $checkInPermission = $this->canCheckInNow($employee, $checkIn);
        if (!$checkInPermission['allowed']) {
            return back()->with('error', $checkInPermission['message']);
        }

        $officeSetting = OfficeSetting::first();

        $distance = null;
        $outsideOffice = false;
        $isSuspicious = false;
        $reasons = [];

        if ($officeSetting) {
            $distance = $this->privacyService->calculateDistanceInMeters(
                $request->latitude,
                $request->longitude,
                $officeSetting->office_latitude,
                $officeSetting->office_longitude
            );

            $outsideOffice = $this->privacyService->isOutsideAllowedRadius(
                $distance,
                $officeSetting->allowed_radius ?? 0
            );
        }

        $photoFile = $request->file('photo');

        if (!$photoFile || !$photoFile->isValid()) {
            return back()->withErrors([
                'photo' => 'A valid live camera photo is required.',
            ]);
        }

        $mime = $photoFile->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            return back()->withErrors([
                'photo' => 'Only JPG, PNG, or WebP images are allowed.',
            ]);
        }

        $originalName = strtolower($photoFile->getClientOriginalName() ?? '');
        if ($originalName && !Str::contains($originalName, ['camera', 'capture', 'selfie', 'photo'])) {
            $reasons[] = 'Uploaded image filename pattern looks unusual for live capture.';
            $isSuspicious = true;
        }

        $photoPath = $photoFile->store('attendance_photos', 'public');

        $shiftStart = Carbon::parse(
            $attendanceDate . ' ' . $shift->start_time,
            'Asia/Karachi'
        );

        $lateThreshold = $shiftStart->copy()->addMinutes($shift->grace_minutes ?? 0);

        $status = 'present';
        $lateMinutes = 0;

        if ($checkIn->gt($lateThreshold)) {
            $status = 'late';
            $lateMinutes = $checkIn->diffInMinutes($shiftStart);
        }

        $agent = new Agent();

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'attendance_date' => $attendanceDate,
            'check_in' => $checkIn->format('H:i:s'),
            'check_out' => null,
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => 0,
            'ip_address' => $request->ip(),
            'device_name' => $agent->device(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'user_agent' => $request->userAgent(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_from_office' => $distance,
            'photo_path' => $photoPath,
            'privacy_note' => $outsideOffice
                ? 'Checked in outside office radius with live camera selfie.'
                : 'Checked in within office radius with live camera selfie.',
            'is_suspicious' => $isSuspicious,
            'suspicious_reason' => !empty($reasons) ? implode(', ', $reasons) : null,
        ]);

        if ($attendance->is_suspicious) {
            $this->appNotificationService->notifyAdmins(
                'suspicious_attendance',
                'Suspicious Attendance Detected',
                "{$employee->full_name} marked suspicious attendance on {$attendance->attendance_date->format('Y-m-d')}. Reason: {$attendance->suspicious_reason}",
                route('attendances.index'),
                [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $employee->id,
                ]
            );
        }

        return redirect()
            ->route('profile.attendance')
            ->with('success', 'Your check-in has been marked successfully.');
    }

    public function checkOutForm()
    {
        abort_unless(auth()->user()->can('mark self checkout'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $user = auth()->user();
        $employee = $user->employee?->load('shift');

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your employee profile is not linked yet.');
        }

        if (!$employee->shift) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No shift assigned to your profile. Please contact admin.');
        }

        $now = Carbon::now('Asia/Karachi');
        $todayAttendance = $this->getCurrentShiftAttendance($employee, $now);

        if (!$todayAttendance) {
            return redirect()
                ->route('profile.checkin.form')
                ->with('error', 'Please mark check-in first.');
        }

        if ($todayAttendance->check_out) {
            return redirect()
                ->route('profile.attendance')
                ->with('error', 'Check-out already submitted for this shift.');
        }

        return view('profile.check-out', compact('employee', 'todayAttendance'));
    }

    public function storeCheckOut(Request $request)
    {
        abort_unless(auth()->user()->can('mark self checkout'), 403);
        abort_unless(feature_enabled('attendance_module_enabled'), 403);

        $user = auth()->user();
        $employee = $user->employee?->load('shift');

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your employee profile is not linked yet.');
        }

        if (!$employee->shift) {
            return back()->with('error', 'No shift assigned to your profile. Please contact admin.');
        }

        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'capture_source' => ['required', 'in:camera'],
        ]);

        $checkOut = Carbon::now('Asia/Karachi');
        $shift = $employee->shift;

        $attendance = $this->getCurrentShiftAttendance($employee, $checkOut);

        if (!$attendance) {
            return redirect()
                ->route('profile.checkin.form')
                ->with('error', 'Please mark check-in first.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Check-out already submitted for this shift.');
        }

        $officeSetting = OfficeSetting::first();

        $distance = null;
        $outsideOffice = false;

        if ($officeSetting) {
            $distance = $this->privacyService->calculateDistanceInMeters(
                $request->latitude,
                $request->longitude,
                $officeSetting->office_latitude,
                $officeSetting->office_longitude
            );

            $outsideOffice = $this->privacyService->isOutsideAllowedRadius(
                $distance,
                $officeSetting->allowed_radius ?? 0
            );
        }

        $photoFile = $request->file('photo');

        if (!$photoFile || !$photoFile->isValid()) {
            return back()->withErrors([
                'photo' => 'A valid live camera photo is required.',
            ]);
        }

        $mime = $photoFile->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            return back()->withErrors([
                'photo' => 'Only JPG, PNG, or WebP images are allowed.',
            ]);
        }

        $isSuspicious = (bool) $attendance->is_suspicious;
        $reasons = [];

        $originalName = strtolower($photoFile->getClientOriginalName() ?? '');
        if ($originalName && !Str::contains($originalName, ['camera', 'capture', 'selfie', 'photo'])) {
            $reasons[] = 'Checkout image filename pattern looks unusual for live capture.';
            $isSuspicious = true;
        }

        $checkoutPhotoPath = $photoFile->store('attendance_photos', 'public');

        $attendanceBaseDate = Carbon::parse(
            $attendance->attendance_date->format('Y-m-d'),
            'Asia/Karachi'
        );

        $shiftStartDateTime = Carbon::parse(
            $attendanceBaseDate->format('Y-m-d') . ' ' . $shift->start_time,
            'Asia/Karachi'
        );

        $shiftEndDateTime = Carbon::parse(
            $attendanceBaseDate->format('Y-m-d') . ' ' . $shift->end_time,
            'Asia/Karachi'
        );

        if ($shift->is_overnight && $shiftEndDateTime->lte($shiftStartDateTime)) {
            $shiftEndDateTime->addDay();
        }

        $checkInDateTime = Carbon::parse(
            $attendanceBaseDate->format('Y-m-d') . ' ' . $attendance->check_in,
            'Asia/Karachi'
        );

        if ($shift->is_overnight && $checkInDateTime->lt($shiftStartDateTime)) {
            $checkInDateTime->addDay();
        }

        $checkOutDateTime = $checkOut->copy();

        if ($checkOutDateTime->lt($checkInDateTime)) {
            $checkOutDateTime->addDay();
        }

        $breakMinutes = 0;

        if ($shift->break_start_time && $shift->break_end_time) {
            $breakStartDateTime = Carbon::parse(
                $attendanceBaseDate->format('Y-m-d') . ' ' . $shift->break_start_time,
                'Asia/Karachi'
            );

            $breakEndDateTime = Carbon::parse(
                $attendanceBaseDate->format('Y-m-d') . ' ' . $shift->break_end_time,
                'Asia/Karachi'
            );

            if ($shift->is_overnight) {
                if ($breakStartDateTime->lt($shiftStartDateTime)) {
                    $breakStartDateTime->addDay();
                }

                if ($breakEndDateTime->lte($breakStartDateTime)) {
                    $breakEndDateTime->addDay();
                }
            }

            $overlapStart = $checkInDateTime->copy()->max($breakStartDateTime);
            $overlapEnd = $checkOutDateTime->copy()->min($breakEndDateTime);

            if ($overlapEnd->gt($overlapStart)) {
                $breakMinutes = $overlapEnd->diffInMinutes($overlapStart);
            }
        }

        $totalWorkedSpan = $checkOutDateTime->diffInMinutes($checkInDateTime);
        $workedMinutes = max(0, $totalWorkedSpan - $breakMinutes);

        $overtimeMinutes = 0;
        if ($checkOutDateTime->gt($shiftEndDateTime)) {
            $overtimeMinutes = $checkOutDateTime->diffInMinutes($shiftEndDateTime);
        }

        $existingReason = trim((string) $attendance->suspicious_reason);
        if (!empty($reasons)) {
            $mergedReasons = array_filter([$existingReason, implode(', ', $reasons)]);
            $suspiciousReason = implode(' | ', $mergedReasons);
        } else {
            $suspiciousReason = $existingReason ?: null;
        }

        $attendance->update([
            'check_out' => $checkOut->format('H:i:s'),
            'overtime_minutes' => $overtimeMinutes,
            'break_minutes' => $breakMinutes,
            'worked_minutes' => $workedMinutes,
            'checkout_latitude' => $request->latitude,
            'checkout_longitude' => $request->longitude,
            'checkout_distance_from_office' => $distance,
            'checkout_photo_path' => $checkoutPhotoPath,
            'checkout_privacy_note' => $outsideOffice
                ? 'Checked out outside office radius with live camera selfie.'
                : 'Checked out within office radius with live camera selfie.',
            'privacy_note' => trim(($attendance->privacy_note ?? '') . ' Check-out submitted by employee.'),
            'is_suspicious' => $isSuspicious,
            'suspicious_reason' => $suspiciousReason,
        ]);

        if ($attendance->fresh()->is_suspicious) {
            $this->appNotificationService->notifyAdmins(
                'suspicious_attendance',
                'Suspicious Attendance Detected',
                "{$employee->full_name} marked suspicious check-out on {$attendance->attendance_date->format('Y-m-d')}. Reason: {$attendance->fresh()->suspicious_reason}",
                route('attendances.index'),
                [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $employee->id,
                ]
            );
        }

        return redirect()
            ->route('profile.attendance')
            ->with('success', 'Your check-out has been submitted successfully.');
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}