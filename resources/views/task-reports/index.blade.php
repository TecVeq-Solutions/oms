<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Task Reports</h2>

                    <form method="GET" action="{{ route('task-reports.index') }}" class="flex items-end gap-3">
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

                @if($users->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Assigned</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Completed</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Consumed</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Allowed</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Approved Extra</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Unapproved Overrun</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Impact Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $row)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $row['user']->name }}</td>
                                        <td class="px-4 py-3">{{ $row['assigned_tasks_count'] }}</td>
                                        <td class="px-4 py-3">{{ $row['completed_tasks_count'] }}</td>
                                        <td class="px-4 py-3">{{ $row['consumed_minutes'] }}</td>
                                        <td class="px-4 py-3">{{ $row['allowed_minutes'] }}</td>
                                        <td class="px-4 py-3">{{ $row['approved_extra_minutes'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="{{ $row['unapproved_overrun_minutes'] > 0 ? 'text-red-700 font-semibold' : 'text-gray-700' }}">
                                                {{ $row['unapproved_overrun_minutes'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $row['impact_score'] }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('task-reports.user', ['user' => $row['user']->id, 'month' => $month]) }}"
                                               class="text-blue-600 hover:underline">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No report data found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>