<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Services\AppNotificationService;

class ProjectController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view projects'), 403);

        $projects = Project::with(['workspace', 'manager'])
            ->when($request->filled('workspace_id'), function ($query) use ($request) {
                $query->where('workspace_id', $request->workspace_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $workspaces = Workspace::where('is_active', true)->orderBy('name')->get();

        return view('projects.index', compact('projects', 'workspaces'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('create project'), 403);

        $workspaces = Workspace::with('users')->where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('projects.create', compact('workspaces', 'users'));
    }

    public function store(StoreProjectRequest $request)
    {
        $workspace = Workspace::with('users')->findOrFail($request->workspace_id);

        if ($request->filled('manager_id') && !$workspace->users->pluck('id')->contains($request->manager_id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'manager_id' => 'Selected manager must be a member of the selected workspace.',
                ]);
        }

        $project = Project::create($request->validated());

        $notifyUserIds = $workspace->users
            ->pluck('id')
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'project_created',
                'New Project Created',
                auth()->user()->name . ' created a new project: ' . $project->name,
                route('projects.show', $project),
                [
                    'project_id' => $project->id,
                    'workspace_id' => $project->workspace_id,
                    'manager_id' => $project->manager_id,
                ]
            );
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        abort_unless(auth()->user()->can('view projects'), 403);

        $project->load([
            'workspace',
            'manager',
            'tasks.assignedUsers',
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        abort_unless(auth()->user()->can('edit project'), 403);

        $workspaces = Workspace::with('users')->where('is_active', true)->orderBy('name')->get();

        $selectedWorkspace = $workspaces->firstWhere('id', $project->workspace_id);
        $users = $selectedWorkspace ? $selectedWorkspace->users->sortBy('name')->values() : collect();

        return view('projects.edit', compact('project', 'workspaces', 'users'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $workspace = Workspace::with('users')->findOrFail($request->workspace_id);

        if ($request->filled('manager_id') && !$workspace->users->pluck('id')->contains($request->manager_id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'manager_id' => 'Selected manager must be a member of the selected workspace.',
                ]);
        }

        $project->update($request->validated());

        $notifyUserIds = $workspace->users
            ->pluck('id')
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'project_updated',
                'Project Updated',
                auth()->user()->name . ' updated project: ' . $project->name,
                route('projects.show', $project),
                [
                    'project_id' => $project->id,
                    'workspace_id' => $project->workspace_id,
                    'manager_id' => $project->manager_id,
                ]
            );
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        abort_unless(auth()->user()->can('delete project'), 403);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}