<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Tasks</h2>

                    @can('create task')
                        <a href="{{ route('tasks.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Create Task
                        </a>
                    @endcan
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Workspace</label>
                        <select name="workspace_id" class="w-full rounded-lg border-gray-300">
                            <option value="">All Workspaces</option>
                            @foreach($workspaces as $workspace)
                                <option value="{{ $workspace->id }}" {{ request('workspace_id') == $workspace->id ? 'selected' : '' }}>
                                    {{ $workspace->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Project</label>
                        <select name="project_id" class="w-full rounded-lg border-gray-300">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">All Statuses</option>
                            @foreach(['todo', 'in_progress', 'on_hold', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Priority</label>
                        <select name="priority" class="w-full rounded-lg border-gray-300">
                            <option value="">All Priorities</option>
                            @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                                <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filter
                        </button>

                        <a href="{{ route('tasks.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>

                @if($tasks->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Title</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Workspace</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Project</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Priority</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Usage</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Overrun</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Assigned Users</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $task->title }}</td>
                                        <td class="px-4 py-3">{{ $task->project->workspace->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $task->project->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ ucfirst($task->priority) }}</td>
                                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $task->status)) }}</td>
                                        @php
                                            $taskConsumedMinutes = (int) $task->timeLogs()->sum('duration_minutes');
                                            $taskAllowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
                                            $taskUnapprovedOverrunMinutes = max(0, $taskConsumedMinutes - $taskAllowedMinutes);
                                        @endphp

                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-700">
                                                {{ $taskConsumedMinutes }} / {{ $taskAllowedMinutes }} min
                                            </div>
                                        </td>

                                        <td class="px-4 py-3">
                                            @if($taskUnapprovedOverrunMinutes > 0)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                    +{{ $taskUnapprovedOverrunMinutes }} min
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    No Overrun
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @forelse($task->assignedUsers as $user)
                                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                    {{ $user->name }}
                                                </span>
                                            @empty
                                                -
                                            @endforelse
                                        </td>
                                        <td class="px-4 py-3 flex gap-2">
                                            <a href="{{ route('tasks.show', $task) }}"
                                               class="text-blue-600 hover:underline">View</a>

                                            @can('edit task')
                                                <a href="{{ route('tasks.edit', $task) }}"
                                                   class="text-yellow-600 hover:underline">Edit</a>
                                            @endcan

                                            @can('delete task')
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                                      onsubmit="return confirm('Delete this task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No tasks found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>