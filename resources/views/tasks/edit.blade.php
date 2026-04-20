<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6"
                 x-data="taskFormEdit()"
                 x-init="init()">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Task</h2>

                <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Workspace</label>
                        <select x-model="workspaceId" @change="loadProjects(true)" class="w-full rounded-lg border-gray-300">
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
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full rounded-lg border-gray-300">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full rounded-lg border-gray-300">
                                @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                                    <option value="{{ $priority }}"
                                        {{ old('priority', $task->priority) === $priority ? 'selected' : '' }}>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300">
                                @foreach(['todo', 'in_progress', 'on_hold', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('status', $task->status) === $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Minutes</label>
                        <input type="number" name="estimated_minutes"
                               value="{{ old('estimated_minutes', $task->estimated_minutes) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date"
                                   value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Users</label>
                        <select name="assigned_users[]" multiple class="w-full rounded-lg border-gray-300 min-h-[180px]">
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id"
                                    :selected="selectedUsers.includes(String(user.id)) || selectedUsers.includes(user.id)"
                                    x-text="`${user.name} (${user.email})`">
                                </option>
                            </template>
                        </select>
                        @error('assigned_users') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Update
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
        function taskFormEdit() {
            return {
                workspaceId: '{{ old('workspace_id', $task->project->workspace_id) }}',
                projectId: '{{ old('project_id', $task->project_id) }}',
                selectedUsers: @json(old('assigned_users', $selectedUsers)),
                projects: [],
                users: [],

                async init() {
                    await this.loadProjects(false);
                    await this.loadUsers();
                },

                async loadProjects(resetProject = false) {
                    this.projects = [];
                    this.users = [];

                    if (!this.workspaceId) return;

                    const response = await fetch(`/task-system/workspaces/${this.workspaceId}/projects`);
                    this.projects = await response.json();

                    if (resetProject) {
                        this.projectId = '';
                    }
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