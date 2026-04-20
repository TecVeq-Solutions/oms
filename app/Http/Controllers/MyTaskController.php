<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskTimeLog;

class MyTaskController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view own tasks'), 403);

        $userId = auth()->id();

        $tasks = Task::with(['project.workspace', 'creator'])
            ->withSum(['timeLogs as my_consumed_minutes' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }], 'duration_minutes')
            ->withExists(['activeTimeLogs as my_running_log_exists' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->withExists(['extensionRequests as my_pending_extension_exists' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('status', 'pending');
            }])
            ->whereHas('assignedUsers', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->latest()
            ->paginate(10);

        $runningTaskLog = TaskTimeLog::with('task')
            ->where('user_id', $userId)
            ->where('is_running', true)
            ->latest('started_at')
            ->first();

        return view('tasks.my-tasks', compact('tasks', 'runningTaskLog'));
    }
}