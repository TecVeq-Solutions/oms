<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Support\TaskActivityLogger;
use App\Services\AppNotificationService;

class TaskController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view tasks'), 403);

        $tasks = Task::with(['project.workspace', 'creator', 'assignedUsers'])
            ->when($request->filled('workspace_id'), function ($query) use ($request) {
                $query->whereHas('project', function ($projectQuery) use ($request) {
                    $projectQuery->where('workspace_id', $request->workspace_id);
                });
            })
            ->when($request->filled('project_id'), function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('priority'), function ($query) use ($request) {
                $query->where('priority', $request->priority);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $workspaces = Workspace::where('is_active', true)->orderBy('name')->get();

        $projects = Project::when($request->filled('workspace_id'), function ($query) use ($request) {
            $query->where('workspace_id', $request->workspace_id);
        })
            ->orderBy('name')
            ->get();

        return view('tasks.index', compact('tasks', 'workspaces', 'projects'));
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->can('create task'), 403);

        $workspaces = Workspace::with('users')->where('is_active', true)->orderBy('name')->get();

        $projects = Project::with('workspace')
            ->whereHas('workspace', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $selectedProject = null;
        $users = collect();

        if ($request->filled('project_id')) {
            $selectedProject = Project::with('workspace.users')->find($request->project_id);

            if ($selectedProject) {
                $users = $selectedProject->workspace->users->sortBy('name')->values();
            }
        }

        return view('tasks.create', compact('projects', 'users', 'selectedProject', 'workspaces'));
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $assignedUsers = $data['assigned_users'] ?? [];
        unset($data['assigned_users']);

        $project = Project::with('workspace.users')->findOrFail($data['project_id']);
        $workspaceMemberIds = $project->workspace->users->pluck('id')->toArray();

        foreach ($assignedUsers as $userId) {
            if (!in_array($userId, $workspaceMemberIds)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'assigned_users' => 'All assigned users must be members of the selected project workspace.',
                    ]);
            }
        }

        $task = Task::create([
            ...$data,
            'created_by' => auth()->id(),
            'completed_at' => $data['status'] === 'completed' ? Carbon::now() : null,
            'is_active' => true,
        ]);

        if (!empty($assignedUsers)) {
            $syncData = [];

            foreach ($assignedUsers as $userId) {
                $syncData[$userId] = [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ];
            }

            $task->assignedUsers()->sync($syncData);
        }

        TaskActivityLogger::log(
            $task,
            auth()->id(),
            'task_created',
            'Task created successfully.',
            [
                'title' => $task->title,
                'project_id' => $task->project_id,
                'assigned_user_ids' => $assignedUsers,
            ]
        );

        $notifyUserIds = collect($assignedUsers)
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_assigned',
                'New Task Assigned',
                auth()->user()->name . ' assigned you a new task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                ]
            );
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        abort_unless(auth()->user()->can('view tasks') || auth()->user()->can('view own tasks'), 403);

        $user = auth()->user();

        $isAssigned = $task->assignedUsers()
            ->where('users.id', $user->id)
            ->exists();

        if (!$user->can('view tasks') && !$isAssigned) {
            abort(403);
        }

        $task->load([
            'project.workspace',
            'creator',
            'assignedUsers',
            'timeLogs.user',
            'extensionRequests.user',
            'extensionRequests.reviewer',
            'comments.user',
            'comments.mentionedUsers',
            'attachments.uploader',
            'activityLogs.user',
        ]);

        $myRunningLog = $task->timeLogs()
            ->where('user_id', $user->id)
            ->where('is_running', true)
            ->latest('started_at')
            ->first();

        $myConsumedMinutes = (int) $task->timeLogs()
            ->where('user_id', $user->id)
            ->sum('duration_minutes');

        $myPendingExtensionRequest = $task->extensionRequests()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $taskConsumedMinutes = (int) $task->timeLogs()->sum('duration_minutes');
        $taskAllowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
        $taskUnapprovedOverrunMinutes = max(0, $taskConsumedMinutes - $taskAllowedMinutes);

        return view('tasks.show', compact(
            'task',
            'myRunningLog',
            'myConsumedMinutes',
            'myPendingExtensionRequest',
            'taskConsumedMinutes',
            'taskAllowedMinutes',
            'taskUnapprovedOverrunMinutes'
        ));
    }

    public function edit(Task $task)
    {
        abort_unless(auth()->user()->can('edit task'), 403);

        $task->load('project.workspace.users');

        $workspaces = Workspace::with('users')->where('is_active', true)->orderBy('name')->get();

        $projects = Project::with('workspace')
            ->whereHas('workspace', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $users = $task->project->workspace->users->sortBy('name')->values();
        $selectedUsers = $task->assignedUsers()->pluck('users.id')->toArray();

        return view('tasks.edit', compact('task', 'projects', 'users', 'selectedUsers', 'workspaces'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        $assignedUsers = $data['assigned_users'] ?? [];
        unset($data['assigned_users']);

        $project = Project::with('workspace.users')->findOrFail($data['project_id']);
        $workspaceMemberIds = $project->workspace->users->pluck('id')->toArray();

        foreach ($assignedUsers as $userId) {
            if (!in_array($userId, $workspaceMemberIds)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'assigned_users' => 'All assigned users must be members of the selected project workspace.',
                    ]);
            }
        }

        $oldAssignedUserIds = $task->assignedUsers()->pluck('users.id')->toArray();

        $oldValues = [
            'title' => $task->title,
            'status' => $task->status,
            'priority' => $task->priority,
            'estimated_minutes' => $task->estimated_minutes,
            'project_id' => $task->project_id,
        ];

        $task->update([
            ...$data,
            'completed_at' => $data['status'] === 'completed'
                ? ($task->completed_at ?? now())
                : null,
        ]);

        $syncData = [];

        foreach ($assignedUsers as $userId) {
            $existingAssignment = $task->assignments()->where('user_id', $userId)->first();

            $syncData[$userId] = [
                'assigned_by' => $existingAssignment?->assigned_by ?? auth()->id(),
                'assigned_at' => $existingAssignment?->assigned_at ?? now(),
            ];
        }

        $task->assignedUsers()->sync($syncData);

        TaskActivityLogger::log(
            $task,
            auth()->id(),
            'task_updated',
            'Task updated successfully.',
            [
                'old' => $oldValues,
                'new' => [
                    'title' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'estimated_minutes' => $task->estimated_minutes,
                    'project_id' => $task->project_id,
                ],
                'assigned_user_ids' => $assignedUsers,
            ]
        );

        $notifyUserIds = collect(array_merge($oldAssignedUserIds, $assignedUsers))
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_updated',
                'Task Updated',
                auth()->user()->name . ' updated the task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                ]
            );
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        abort_unless(auth()->user()->can('delete task'), 403);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}