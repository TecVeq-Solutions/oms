<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">My Task Report</h2>

                    <form method="GET" action="{{ route('task-reports.my') }}" class="flex items-end gap-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Month</label>
                            <input type="month" name="month" value="{{ $month }}" class="rounded-lg border-gray-300">
                        </div>

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Assigned Tasks</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $summary['assigned_tasks_count'] }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Completed Tasks</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $summary['completed_tasks_count'] }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Impact Score</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $summary['impact_score'] }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Consumed Minutes</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $summary['consumed_minutes'] }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Allowed Minutes</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">{{ $summary['allowed_minutes'] }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Unapproved Overrun</p>
                        <p class="text-xl font-bold {{ $summary['unapproved_overrun_minutes'] > 0 ? 'text-red-700' : 'text-gray-800' }} mt-1">
                            {{ $summary['unapproved_overrun_minutes'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Task Detail</h3>

                @if($tasks->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Task</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Workspace / Project</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Consumed</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Allowed</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Approved Extra</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Unapproved Overrun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $row)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('tasks.show', $row['task']) }}" class="text-blue-600 hover:underline">
                                                {{ $row['task']->title }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $row['task']->project->workspace->name ?? '-' }} /
                                            {{ $row['task']->project->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $row['task']->status)) }}</td>
                                        <td class="px-4 py-3">{{ $row['my_consumed_minutes'] }}</td>
                                        <td class="px-4 py-3">{{ $row['allowed_minutes'] }}</td>
                                        <td class="px-4 py-3">{{ $row['approved_extra_minutes'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="{{ $row['unapproved_overrun_minutes'] > 0 ? 'text-red-700 font-semibold' : 'text-gray-700' }}">
                                                {{ $row['unapproved_overrun_minutes'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No tasks found for this report.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>