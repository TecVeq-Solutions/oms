<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\Request;

class TaskSystemLookupController extends Controller
{
    public function projectsByWorkspace(Workspace $workspace)
    {
        abort_unless(
            auth()->user()->can('view projects') || auth()->user()->can('create task') || auth()->user()->can('edit task'),
            403
        );

        $projects = $workspace->projects()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($projects);
    }

    public function usersByProject(Project $project)
    {
        abort_unless(
            auth()->user()->can('view tasks') || auth()->user()->can('create task') || auth()->user()->can('edit task'),
            403
        );

        $users = $project->workspace
            ->users()
            ->orderBy('name')
            ->get(['users.id', 'users.name', 'users.email']);

        return response()->json($users);
    }
}