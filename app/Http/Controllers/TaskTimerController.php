<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTimeLog;
use Illuminate\Support\Facades\DB;
use App\Support\TaskActivityLogger;
use App\Services\AppNotificationService;

class TaskTimerController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function start(Task $task)
    {
        abort_unless(auth()->user()->can('start own task'), 403);

        $user = auth()->user();

        $isAssigned = $task->assignedUsers()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        if ($task->status === 'completed' || $task->status === 'cancelled') {
            return back()->with('error', 'You cannot start a completed or cancelled task.');
        }

        $alreadyRunningThisTask = TaskTimeLog::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('is_running', true)
            ->exists();

        if ($alreadyRunningThisTask) {
            return back()->with('error', 'This task is already running.');
        }

        $otherRunningLog = TaskTimeLog::with('task')
            ->where('user_id', $user->id)
            ->where('is_running', true)
            ->first();

        if ($otherRunningLog) {
            return back()->with('error', 'Please stop your currently running task first: ' . $otherRunningLog->task->title);
        }

        DB::transaction(function () use ($task, $user) {
            TaskTimeLog::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'stopped_at' => null,
                'duration_minutes' => 0,
                'is_running' => true,
            ]);

            if (in_array($task->status, ['todo', 'on_hold'])) {
                $task->update([
                    'status' => 'in_progress',
                ]);
            }

            TaskActivityLogger::log(
                $task,
                $user->id,
                'task_started',
                'Task timer started.',
                [
                    'started_at' => now()->toDateTimeString(),
                ]
            );
        });

        $notifyUserIds = $task->assignedUsers()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_started',
                'Task Started',
                $user->name . ' started working on task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                    'started_by' => $user->id,
                ]
            );
        }

        return back()->with('success', 'Task started successfully.');
    }

    public function stop(Task $task)
    {
        abort_unless(auth()->user()->can('stop own task'), 403);

        $user = auth()->user();

        $runningLog = TaskTimeLog::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('is_running', true)
            ->latest('started_at')
            ->first();

        if (!$runningLog) {
            return back()->with('error', 'No running session found for this task.');
        }

        $stoppedAt = now();
        $durationMinutes = max(1, $runningLog->started_at->diffInMinutes($stoppedAt));

        $runningLog->update([
            'stopped_at' => $stoppedAt,
            'duration_minutes' => $durationMinutes,
            'is_running' => false,
        ]);

        TaskActivityLogger::log(
            $task,
            $user->id,
            'task_stopped',
            'Task timer stopped.',
            [
                'started_at' => $runningLog->started_at?->toDateTimeString(),
                'stopped_at' => $stoppedAt->toDateTimeString(),
                'duration_minutes' => $durationMinutes,
            ]
        );

        $notifyUserIds = $task->assignedUsers()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_stopped',
                'Task Stopped',
                $user->name . ' stopped the task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                    'stopped_by' => $user->id,
                    'duration_minutes' => $durationMinutes,
                ]
            );
        }

        return back()->with('success', 'Task stopped successfully.');
    }

    public function complete(Task $task)
    {
        abort_unless(auth()->user()->can('complete own task'), 403);

        $user = auth()->user();

        $isAssigned = $task->assignedUsers()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        if ($task->status === 'completed') {
            return back()->with('error', 'Task is already completed.');
        }

        DB::transaction(function () use ($task, $user) {
            $runningLog = TaskTimeLog::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->where('is_running', true)
                ->latest('started_at')
                ->first();

            if ($runningLog) {
                $stoppedAt = now();
                $durationMinutes = max(1, $runningLog->started_at->diffInMinutes($stoppedAt));

                $runningLog->update([
                    'stopped_at' => $stoppedAt,
                    'duration_minutes' => $durationMinutes,
                    'is_running' => false,
                ]);
            }

            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            TaskActivityLogger::log(
                $task,
                $user->id,
                'task_completed',
                'Task marked as completed.',
                [
                    'completed_at' => now()->toDateTimeString(),
                ]
            );
        });

        $notifyUserIds = $task->assignedUsers()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_completed',
                'Task Completed',
                $user->name . ' completed the task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                    'completed_by' => $user->id,
                ]
            );
        }

        return back()->with('success', 'Task completed successfully.');
    }
}