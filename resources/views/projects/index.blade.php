<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Projects</h2>

                    @can('create project')
                        <a href="{{ route('projects.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Create Project
                        </a>
                    @endcan
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('projects.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
                        <label class="block text-sm text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">All Statuses</option>
                            @foreach(['planned', 'active', 'on_hold', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filter
                        </button>

                        <a href="{{ route('projects.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>

                @if($projects->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Workspace</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Manager</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $project->name }}</td>
                                        <td class="px-4 py-3">{{ $project->workspace->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $project->manager->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $project->status)) }}</td>
                                        <td class="px-4 py-3 flex gap-2">
                                            <a href="{{ route('projects.show', $project) }}"
                                               class="text-blue-600 hover:underline">View</a>

                                            @can('edit project')
                                                <a href="{{ route('projects.edit', $project) }}"
                                                   class="text-yellow-600 hover:underline">Edit</a>
                                            @endcan

                                            @can('delete project')
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                                      onsubmit="return confirm('Delete this project?')">
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
                        {{ $projects->links() }}
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No projects found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>