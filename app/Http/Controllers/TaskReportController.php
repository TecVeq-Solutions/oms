<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view task reports'), 403);

        $month = $request->input('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);

        $start = Carbon::createFromDate((int) $year, (int) $monthNumber, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $users = User::whereHas('taskAssignments')
            ->with(['taskAssignments.task.project.workspace'])
            ->get()
            ->map(function ($user) use ($start, $end) {
                $assignedTaskIds = $user->taskAssignments()
                    ->whereHas('task', function ($query) use ($start, $end) {
                        $query->where(function ($taskQuery) use ($start, $end) {
                            $taskQuery->whereBetween('created_at', [$start, $end])
                                ->orWhereBetween('completed_at', [$start, $end])
                                ->orWhereBetween('due_date', [$start->toDateString(), $end->toDateString()]);
                        });
                    })
                    ->pluck('task_id')
                    ->unique();

                $tasks = Task::with('timeLogs')
                    ->whereIn('id', $assignedTaskIds)
                    ->get();

                $consumedMinutes = TaskTimeLog::where('user_id', $user->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('duration_minutes');

                $allowedMinutes = $tasks->sum(fn ($task) => (int) $task->estimated_minutes + (int) $task->approved_extra_minutes);
                $approvedExtraMinutes = $tasks->sum('approved_extra_minutes');

                $completedTasks = $tasks->where('status', 'completed')->count();
                $overdueTasks = $tasks->filter(fn ($task) => $task->due_date && now()->gt($task->due_date) && $task->status !== 'completed')->count();

                $unapprovedOverrunMinutes = max(0, $consumedMinutes - $allowedMinutes);

                $impactScore = ($completedTasks * 10) - ceil($unapprovedOverrunMinutes / 30) - ($overdueTasks * 5);

                return [
                    'user' => $user,
                    'assigned_tasks_count' => $tasks->count(),
                    'completed_tasks_count' => $completedTasks,
                    'overdue_tasks_count' => $overdueTasks,
                    'consumed_minutes' => (int) $consumedMinutes,
                    'allowed_minutes' => (int) $allowedMinutes,
                    'approved_extra_minutes' => (int) $approvedExtraMinutes,
                    'unapproved_overrun_minutes' => (int) $unapprovedOverrunMinutes,
                    'impact_score' => (int) $impactScore,
                ];
            })
            ->sortByDesc('impact_score')
            ->values();

        return view('task-reports.index', compact('users', 'month'));
    }

    public function myReport(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $user = auth()->user();
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);

        $start = Carbon::createFromDate((int) $year, (int) $monthNumber, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $assignedTaskIds = $user->taskAssignments()
            ->pluck('task_id')
            ->unique();

        $tasks = Task::with(['project.workspace', 'timeLogs' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->whereIn('id', $assignedTaskIds)
            ->get()
            ->map(function ($task) use ($user) {
                $myConsumedMinutes = (int) $task->timeLogs->sum('duration_minutes');
                $allowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
                $unapprovedOverrunMinutes = max(0, $myConsumedMinutes - $allowedMinutes);

                return [
                    'task' => $task,
                    'my_consumed_minutes' => $myConsumedMinutes,
                    'allowed_minutes' => $allowedMinutes,
                    'approved_extra_minutes' => (int) $task->approved_extra_minutes,
                    'unapproved_overrun_minutes' => $unapprovedOverrunMinutes,
                ];
            });

        $summary = [
            'assigned_tasks_count' => $tasks->count(),
            'completed_tasks_count' => $tasks->filter(fn ($row) => $row['task']->status === 'completed')->count(),
            'consumed_minutes' => (int) $tasks->sum('my_consumed_minutes'),
            'allowed_minutes' => (int) $tasks->sum('allowed_minutes'),
            'approved_extra_minutes' => (int) $tasks->sum('approved_extra_minutes'),
            'unapproved_overrun_minutes' => (int) $tasks->sum('unapproved_overrun_minutes'),
        ];

        $summary['impact_score'] = ($summary['completed_tasks_count'] * 10) - ceil($summary['unapproved_overrun_minutes'] / 30);

        return view('task-reports.my-report', compact('tasks', 'summary', 'month'));
    }

    public function userReport(Request $request, User $user)
    {
        abort_unless(auth()->user()->can('view task reports'), 403);

        $month = $request->input('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);

        $start = Carbon::createFromDate((int) $year, (int) $monthNumber, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $assignedTaskIds = $user->taskAssignments()
            ->pluck('task_id')
            ->unique();

        $tasks = Task::with(['project.workspace', 'timeLogs' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->whereIn('id', $assignedTaskIds)
            ->latest()
            ->get()
            ->map(function ($task) {
                $consumedMinutes = (int) $task->timeLogs->sum('duration_minutes');
                $allowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
                $unapprovedOverrunMinutes = max(0, $consumedMinutes - $allowedMinutes);

                return [
                    'task' => $task,
                    'consumed_minutes' => $consumedMinutes,
                    'allowed_minutes' => $allowedMinutes,
                    'approved_extra_minutes' => (int) $task->approved_extra_minutes,
                    'unapproved_overrun_minutes' => $unapprovedOverrunMinutes,
                ];
            });

        $summary = [
            'assigned_tasks_count' => $tasks->count(),
            'completed_tasks_count' => $tasks->filter(fn ($row) => $row['task']->status === 'completed')->count(),
            'consumed_minutes' => (int) $tasks->sum('consumed_minutes'),
            'allowed_minutes' => (int) $tasks->sum('allowed_minutes'),
            'approved_extra_minutes' => (int) $tasks->sum('approved_extra_minutes'),
            'unapproved_overrun_minutes' => (int) $tasks->sum('unapproved_overrun_minutes'),
        ];

        $summary['impact_score'] = ($summary['completed_tasks_count'] * 10) - ceil($summary['unapproved_overrun_minutes'] / 30);

        return view('task-reports.user-report', compact('user', 'tasks', 'summary', 'month'));
    }
}