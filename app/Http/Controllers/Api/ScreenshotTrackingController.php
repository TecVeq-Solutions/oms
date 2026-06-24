<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeSetting;
use App\Models\Screenshot;
use Carbon\Carbon;

class ScreenshotTrackingController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'active_window_title' => 'nullable|string',
            'active_process_name' => 'nullable|string',
        ]);

        $employee = $request->tracked_employee;
        
        // Find today's active attendance
        $attendance = $employee->attendances()
            ->whereDate('attendance_date', now()->toDateString())
            ->whereNull('check_out')
            ->first();

        $file = $request->file('image');
        $dateFolder = now()->format('Y-m-d');
        $timeStr = now()->format('H-i-s');
        $random = \Str::random(6);
        $empCode = $employee->employee_code ?? 'EMP' . $employee->id;
        
        $filename = "{$empCode}_{$timeStr}_{$random}." . $file->getClientOriginalExtension();
        $path = $file->storeAs("screenshots/{$dateFolder}", $filename, 'public');

        $screenshot = Screenshot::create([
            'employee_id' => $employee->id,
            'attendance_id' => $attendance ? $attendance->id : null,
            'filename' => $filename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'active_window_title' => $request->active_window_title,
            'active_process_name' => $request->active_process_name,
            'captured_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'screenshot_id' => $screenshot->id,
            'message' => 'Screenshot uploaded successfully',
        ]);
    }

    public function config(Request $request)
    {
        $employee = $request->tracked_employee;
        $settings = OfficeSetting::first();
        
        $config = $settings ? $settings->getTrackingConfig() : [
            'office_start' => '09:00:00',
            'office_end' => '18:00:00',
            'interval_seconds' => 600,
            'compression_quality' => 60,
        ];

        $shift = $employee->shift;
        if ($shift) {
            $config['shift_start'] = $shift->start_time;
            $config['shift_end'] = $shift->end_time;
            $config['grace_minutes'] = $shift->grace_minutes;
        }

        return response()->json($config);
    }

    public function heartbeat(Request $request)
    {
        return response()->json([
            'status' => 'alive',
            'server_time' => now()->toDateTimeString()
        ]);
    }

    public function todayScreenshots(Request $request)
    {
        $employee = $request->tracked_employee;
        
        $screenshots = Screenshot::where('employee_id', $employee->id)
            ->whereDate('captured_at', now()->toDateString())
            ->orderBy('captured_at', 'desc')
            ->get(['id', 'filename', 'captured_at', 'active_window_title', 'active_process_name']);

        return response()->json([
            'count' => $screenshots->count(),
            'screenshots' => $screenshots
        ]);
    }
}
