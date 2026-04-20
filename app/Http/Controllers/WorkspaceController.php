<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Services\AppNotificationService;

class WorkspaceController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function index()
    {
        abort_unless(auth()->user()->can('view workspaces'), 403);

        $workspaces = Workspace::with('creator')
            ->latest()
            ->paginate(10);

        return view('workspaces.index', compact('workspaces'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('create workspace'), 403);

        $users = User::orderBy('name')->get();

        return view('workspaces.create', compact('users'));
    }

    public function store(StoreWorkspaceRequest $request)
    {
        $workspace = Workspace::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $memberIds = $request->input('members', []);

        $syncData = [];
        foreach ($memberIds as $userId) {
            $syncData[$userId] = ['role_in_workspace' => 'member'];
        }

        $syncData[auth()->id()] = ['role_in_workspace' => 'admin'];

        $workspace->users()->sync($syncData);

        $notifyUserIds = collect(array_keys($syncData))
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'workspace_created',
                'New Workspace Created',
                auth()->user()->name . ' added you to workspace: ' . $workspace->name,
                route('workspaces.show', $workspace),
                [
                    'workspace_id' => $workspace->id,
                    'created_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->route('workspaces.index')
            ->with('success', 'Workspace created successfully.');
    }

    public function show(Workspace $workspace)
    {
        abort_unless(auth()->user()->can('view workspaces'), 403);

        $workspace->load(['creator', 'users', 'projects']);

        return view('workspaces.show', compact('workspace'));
    }

    public function edit(Workspace $workspace)
    {
        abort_unless(auth()->user()->can('edit workspace'), 403);

        $users = User::orderBy('name')->get();
        $selectedMembers = $workspace->users()->pluck('users.id')->toArray();

        return view('workspaces.edit', compact('workspace', 'users', 'selectedMembers'));
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
    {
        $oldMemberIds = $workspace->users()->pluck('users.id')->toArray();

        $workspace->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $memberIds = $request->input('members', []);

        $syncData = [];
        foreach ($memberIds as $userId) {
            $syncData[$userId] = ['role_in_workspace' => 'member'];
        }

        $syncData[$workspace->created_by] = ['role_in_workspace' => 'admin'];

        $workspace->users()->sync($syncData);

        $notifyUserIds = collect(array_merge($oldMemberIds, array_keys($syncData)))
            ->filter(fn ($id) => (int) $id !== (int) auth()->id())
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'workspace_updated',
                'Workspace Updated',
                auth()->user()->name . ' updated workspace: ' . $workspace->name,
                route('workspaces.show', $workspace),
                [
                    'workspace_id' => $workspace->id,
                    'updated_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->route('workspaces.index')
            ->with('success', 'Workspace updated successfully.');
    }

    public function destroy(Workspace $workspace)
    {
        abort_unless(auth()->user()->can('delete workspace'), 403);

        $workspace->delete();

        return redirect()
            ->route('workspaces.index')
            ->with('success', 'Workspace deleted successfully.');
    }
}