<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Project Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Workspace</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->workspace->name ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Manager</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->manager->name ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Project Name</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ ucwords(str_replace('_', ' ', $project->status)) }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->start_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">End Date</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->end_date?->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $project->description ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Tasks</h3>

                    @if($project->tasks->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($project->tasks as $task)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-semibold text-gray-800">{{ $task->title }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ ucwords(str_replace('_', ' ', $task->status)) }}</p>
                                    <p class="text-sm text-gray-500 mt-1">Priority: {{ ucfirst($task->priority) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                            No tasks found in this project.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>