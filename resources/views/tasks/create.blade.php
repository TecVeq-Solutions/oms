<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6"
                 x-data="taskFormCreate()">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Task</h2>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Workspace</label>
                        <select x-model="workspaceId" @change="loadProjects()" class="w-full rounded-lg border-gray-300">
                            <option value="">Select Workspace</option>
                            @foreach($workspaces as $workspace)
                                <option value="{{ $workspace->id }}">{{ $workspace->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select name="project_id" x-model="projectId" @change="loadUsers()" class="w-full rounded-lg border-gray-300">
                            <option value="">Select Project</option>
                            <template x-for="project in projects" :key="project.id">
                                <option :value="project.id" x-text="project.name"></option>
                            </template>
                        </select>
                        @error('project_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border-gray-300">
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full rounded-lg border-gray-300">
                                @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                                    <option value="{{ $priority }}" {{ old('priority', 'medium') === $priority ? 'selected' : '' }}>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300">
                                @foreach(['todo', 'in_progress', 'on_hold', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ old('status', 'todo') === $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Minutes</label>
                        <input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', 0) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Users</label>
                        <select name="assigned_users[]" multiple class="w-full rounded-lg border-gray-300 min-h-[180px]">
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="`${user.name} (${user.email})`"></option>
                            </template>
                        </select>
                        @error('assigned_users') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Save
                        </button>

                        <a href="{{ route('tasks.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function taskFormCreate() {
            return {
                workspaceId: '',
                projectId: '{{ old('project_id', '') }}',
                projects: [],
                users: [],

                async loadProjects() {
                    this.projectId = '';
                    this.projects = [];
                    this.users = [];

                    if (!this.workspaceId) return;

                    const response = await fetch(`/task-system/workspaces/${this.workspaceId}/projects`);
                    this.projects = await response.json();
                },

                async loadUsers() {
                    this.users = [];

                    if (!this.projectId) return;

                    const response = await fetch(`/task-system/projects/${this.projectId}/users`);
                    this.users = await response.json();
                }
            }
        }
    </script>
</x-app-layout>