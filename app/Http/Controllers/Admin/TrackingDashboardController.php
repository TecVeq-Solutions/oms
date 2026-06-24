<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Screenshot;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TrackingDashboardController extends Controller
{
    public function index()
    {
        $trackedEmployees = Employee::tracked()->with('latestScreenshot', 'shift')->get();
        $totalTracked = $trackedEmployees->count();
        $onlineNow = $trackedEmployees->filter->is_online->count();
        $todayScreenshots = Screenshot::whereDate('captured_at', now()->toDateString())->count();
        
        $settings = \App\Models\OfficeSetting::first();
        $interval = $settings->screenshot_interval_minutes ?? 10;

        return view('admin.tracking.dashboard', compact(
            'trackedEmployees', 'totalTracked', 'onlineNow', 'todayScreenshots', 'interval'
        ));
    }

    public function employeeScreenshots(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $date = $request->input('date', now()->toDateString());

        $screenshots = Screenshot::where('employee_id', $employee->id)
            ->whereDate('captured_at', $date)
            ->with('attendance')
            ->orderBy('captured_at', 'desc')
            ->paginate(24);

        // Daily count for the last 30 days
        $startDate = now()->subDays(30)->toDateString();
        $dailyCounts = Screenshot::where('employee_id', $employee->id)
            ->whereDate('captured_at', '>=', $startDate)
            ->selectRaw('DATE(captured_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.tracking.employee-screenshots', compact('employee', 'screenshots', 'date', 'dailyCounts'));
    }

    public function toggleTracking($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_tracked = !$employee->is_tracked;
        
        if ($employee->is_tracked && !$employee->tracking_api_token) {
            $employee->tracking_api_token = Str::random(64);
        }
        
        $employee->save();

        return back()->with('success', 'Tracking status updated for ' . $employee->full_name);
    }

    public function regenerateToken($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->tracking_api_token = Str::random(64);
        $employee->save();

        return back()->with('success', 'API Token regenerated for ' . $employee->full_name);
    }

    public function getScreenshotsJson(Request $request)
    {
        $query = Screenshot::with('employee:id,full_name,employee_code');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('captured_at', $request->date);
        } else {
            $query->whereDate('captured_at', now()->toDateString());
        }

        $screenshots = $query->orderBy('captured_at', 'desc')->paginate(20);

        return response()->json($screenshots);
    }

    public function getEmployeeStatus()
    {
        $employees = Employee::tracked()->get()->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->full_name,
                'is_online' => $emp->is_online,
                'last_heartbeat' => $emp->last_tracking_heartbeat ? $emp->last_tracking_heartbeat->diffForHumans() : 'Never',
            ];
        });

        return response()->json($employees);
    }
}
