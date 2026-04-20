<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">My Tasks</h2>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if($runningTaskLog)
                    <div class="mb-6 rounded-lg bg-blue-100 text-blue-800 px-4 py-3">
                        Currently running task:
                        <span class="font-semibold">{{ $runningTaskLog->task->title }}</span>
                        since {{ $runningTaskLog->started_at->format('Y-m-d h:i A') }}
                    </div>
                @endif

                @if($tasks->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($tasks as $task)
                            @php
                                $runningLog = $task->timeLogs()
                                    ->where('user_id', auth()->id())
                                    ->where('is_running', true)
                                    ->latest('started_at')
                                    ->first();

                                $consumedMinutes = (int) ($task->my_consumed_minutes ?? 0);
                                $allowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
                                $remainingMinutes = max(0, $allowedMinutes - $consumedMinutes);
                                $unapprovedOverrunMinutes = max(0, $consumedMinutes - $allowedMinutes);
                                $progressPercentage = $allowedMinutes > 0
                                    ? min(100, round(($consumedMinutes / $allowedMinutes) * 100, 2))
                                    : 0;
                            @endphp

                            <div class="bg-gray-50 rounded-lg p-5 border border-gray-100"
                                 @if($runningLog)
                                 x-data="taskTimer('{{ $runningLog->started_at->toIso8601String() }}')"
                                 @endif>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-lg font-bold text-gray-800">{{ $task->title }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $task->project->workspace->name ?? '-' }} / {{ $task->project->name ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            @if($task->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($task->priority === 'high') bg-orange-100 text-orange-700
                                            @elseif($task->priority === 'medium') bg-blue-100 text-blue-700
                                            @else bg-gray-200 text-gray-700 @endif">
                                            {{ ucfirst($task->priority) }}
                                        </span>

                                        @if($task->my_pending_extension_exists)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                Extension Pending
                                            </span>
                                        @endif

                                        @if($unapprovedOverrunMinutes > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                Overrun: +{{ $unapprovedOverrunMinutes }} min
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full {{ $unapprovedOverrunMinutes > 0 ? 'bg-red-500' : 'bg-indigo-600' }}"
                                             style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $consumedMinutes }} / {{ $allowedMinutes }} minutes used
                                    </p>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-2">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Status:</span>
                                        {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                    </p>

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Estimated Minutes:</span>
                                        {{ $task->estimated_minutes }}
                                    </p>

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Approved Extra Minutes:</span>
                                        {{ $task->approved_extra_minutes }}
                                    </p>

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Allowed Minutes:</span>
                                        {{ $allowedMinutes }}
                                    </p>

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Consumed Minutes:</span>
                                        {{ $consumedMinutes }}
                                    </p>

                                    <p class="text-sm {{ $unapprovedOverrunMinutes > 0 ? 'text-red-700 font-semibold' : 'text-gray-700' }}">
                                        <span class="font-semibold">Remaining Allowed Minutes:</span>
                                        {{ $remainingMinutes }}
                                    </p>

                                    <p class="text-sm {{ $unapprovedOverrunMinutes > 0 ? 'text-red-700 font-semibold' : 'text-gray-700' }}">
                                        <span class="font-semibold">Unapproved Overrun Minutes:</span>
                                        {{ $unapprovedOverrunMinutes }}
                                    </p>

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Running:</span>
                                        {{ $task->my_running_log_exists ? 'Yes' : 'No' }}
                                    </p>

                                    @if($runningLog)
                                        <p class="text-sm text-green-700 font-semibold">
                                            Current Session:
                                            <span x-text="formatted"></span>
                                        </p>
                                    @endif

                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Due Date:</span>
                                        {{ $task->due_date?->format('Y-m-d') ?? '-' }}
                                    </p>
                                </div>

                                <div class="mt-4 flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('tasks.show', $task) }}"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        View Details
                                    </a>

                                    @if($task->status !== 'completed')
                                        @if($task->my_running_log_exists)
                                            <form action="{{ route('tasks.stop', $task) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                    Stop
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('tasks.start', $task) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                    Start / Resume
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                              onsubmit="return confirm('Mark this task as completed?')">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                                Complete
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @can('request task extension')
                                    @if($task->status !== 'completed' && !$task->my_pending_extension_exists)
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <form action="{{ route('tasks.extension-requests.store', $task) }}" method="POST" class="space-y-3">
                                                @csrf

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Request Extra Minutes
                                                    </label>
                                                    <input type="number"
                                                           name="requested_extra_minutes"
                                                           min="1"
                                                           class="w-full rounded-lg border-gray-300"
                                                           placeholder="e.g. 60">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Reason
                                                    </label>
                                                    <textarea name="reason"
                                                              rows="3"
                                                              class="w-full rounded-lg border-gray-300"
                                                              placeholder="Why do you need extra time?"></textarea>
                                                </div>

                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                                                    Request Extension
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No tasks assigned yet.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function taskTimer(startedAt) {
            return {
                startedAt: new Date(startedAt),
                formatted: '00:00:00',
                interval: null,

                init() {
                    this.update();
                    this.interval = setInterval(() => this.update(), 1000);
                },

                update() {
                    const now = new Date();
                    const diffMs = now - this.startedAt;

                    const totalSeconds = Math.max(0, Math.floor(diffMs / 1000));
                    const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(totalSeconds % 60).padStart(2, '0');

                    this.formatted = `${hours}:${minutes}:${seconds}`;
                }
            }
        }
    </script>
</x-app-layout>