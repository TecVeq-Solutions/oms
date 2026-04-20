<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Project</h2>

                <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Workspace</label>
                        <select name="workspace_id" class="w-full rounded-lg border-gray-300">
                            @foreach($workspaces as $workspace)
                                <option value="{{ $workspace->id }}"
                                    {{ old('workspace_id', $project->workspace_id) == $workspace->id ? 'selected' : '' }}>
                                    {{ $workspace->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name', $project->name) }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                        <select name="manager_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Select Manager</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('manager_id', $project->manager_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Manager should be selected from the chosen workspace members.
                        </p>
                        @error('manager_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date"
                                   value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date"
                                   value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            @foreach(['planned', 'active', 'on_hold', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $project->status) === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Update
                        </button>

                        <a href="{{ route('projects.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>