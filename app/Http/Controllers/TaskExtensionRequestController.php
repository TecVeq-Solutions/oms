<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskExtensionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\TaskActivityLogger;
use App\Services\AppNotificationService;

class TaskExtensionRequestController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {}

    public function index()
    {
        abort_unless(auth()->user()->can('approve task extension'), 403);

        $requests = TaskExtensionRequest::with(['task.project.workspace', 'user', 'reviewer'])
            ->latest()
            ->paginate(10);

        return view('task-extension-requests.index', compact('requests'));
    }

    public function store(Request $request, Task $task)
    {
        abort_unless(auth()->user()->can('request task extension'), 403);

        $user = auth()->user();

        $isAssigned = $task->assignedUsers()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        if ($task->status === 'completed' || $task->status === 'cancelled') {
            return back()->with('error', 'Extension request cannot be submitted.');
        }

        $validated = $request->validate([
            'requested_extra_minutes' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $pendingExists = TaskExtensionRequest::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return back()->with('error', 'You already have a pending request.');
        }

        $extension = TaskExtensionRequest::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'requested_extra_minutes' => $validated['requested_extra_minutes'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        TaskActivityLogger::log(
            $task,
            $user->id,
            'extension_requested',
            'Task extension requested.',
            [
                'requested_extra_minutes' => $validated['requested_extra_minutes'],
                'reason' => $validated['reason'],
            ]
        );

        // 🔔 Notify Admins / Managers (workspace users except requester)
        $notifyUserIds = $task->project->workspace->users()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'extension_requested',
                'Extension Request',
                $user->name . ' requested extension for task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'request_id' => $extension->id,
                ]
            );
        }

        return back()->with('success', 'Extension request submitted successfully.');
    }

    public function approve(Request $request, TaskExtensionRequest $taskExtensionRequest)
    {
        abort_unless(auth()->user()->can('approve task extension'), 403);

        if ($taskExtensionRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($taskExtensionRequest, $validated) {
            $taskExtensionRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $validated['review_note'] ?? null,
            ]);

            $task = $taskExtensionRequest->task;

            $task->update([
                'approved_extra_minutes' =>
                    $task->approved_extra_minutes + $taskExtensionRequest->requested_extra_minutes,
            ]);

            TaskActivityLogger::log(
                $task,
                auth()->id(),
                'extension_approved',
                'Task extension approved.',
                [
                    'approved_extra_minutes' => $taskExtensionRequest->requested_extra_minutes,
                ]
            );
        });

        $task = $taskExtensionRequest->task;

        // 🔔 Notify Request Owner
        $this->appNotificationService->notifyUsers(
            [$taskExtensionRequest->user_id],
            'extension_approved',
            'Extension Approved',
            auth()->user()->name . ' approved your extension request for task: ' . $task->title,
            route('tasks.show', $task),
            [
                'task_id' => $task->id,
                'request_id' => $taskExtensionRequest->id,
            ]
        );

        return back()->with('success', 'Extension request approved successfully.');
    }

    public function reject(Request $request, TaskExtensionRequest $taskExtensionRequest)
    {
        abort_unless(auth()->user()->can('approve task extension'), 403);

        if ($taskExtensionRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $taskExtensionRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        TaskActivityLogger::log(
            $taskExtensionRequest->task,
            auth()->id(),
            'task_extension_rejected',
            'Task extension request rejected.',
            [
                'request_id' => $taskExtensionRequest->id,
            ]
        );

        $task = $taskExtensionRequest->task;

        // 🔔 Notify Request Owner
        $this->appNotificationService->notifyUsers(
            [$taskExtensionRequest->user_id],
            'extension_rejected',
            'Extension Rejected',
            auth()->user()->name . ' rejected your extension request for task: ' . $task->title,
            route('tasks.show', $task),
            [
                'task_id' => $task->id,
                'request_id' => $taskExtensionRequest->id,
            ]
        );

        return back()->with('success', 'Extension request rejected successfully.');
    }
}