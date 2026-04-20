<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $allowedMinutes = (int) $task->estimated_minutes + (int) $task->approved_extra_minutes;
                $remainingAllowedMinutes = max(0, $allowedMinutes - (int) ($myConsumedMinutes ?? 0));
                $myUnapprovedOverrunMinutes = max(0, (int) ($myConsumedMinutes ?? 0) - $allowedMinutes);
                $taskRemainingAllowedMinutes = max(0, $taskAllowedMinutes - $taskConsumedMinutes);
            @endphp

            <div class="bg-white shadow rounded-xl p-6" @if(isset($myRunningLog) && $myRunningLog)
            x-data="taskTimer('{{ $myRunningLog->started_at->toIso8601String() }}')" @endif>
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Task Details</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $task->project->workspace->name ?? '-' }} / {{ $task->project->name ?? '-' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($task->status !== 'completed')
                            @if(isset($myRunningLog) && $myRunningLog)
                                <form action="{{ route('tasks.stop', $task) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        Stop Task
                                    </button>
                                </form>
                            @elseif($task->assignedUsers->pluck('id')->contains(auth()->id()))
                                <form action="{{ route('tasks.start', $task) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Start / Resume
                                    </button>
                                </form>
                            @endif

                            @if($task->assignedUsers->pluck('id')->contains(auth()->id()))
                                <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                    onsubmit="return confirm('Mark this task as completed?')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Complete Task
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                                Completed
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Title</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $task->title }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Priority</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ ucfirst($task->priority) }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">
                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Estimated Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $task->estimated_minutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Approved Extra Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $task->approved_extra_minutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Allowed Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $allowedMinutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">My Consumed Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $myConsumedMinutes ?? 0 }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">My Remaining Allowed Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $remainingAllowedMinutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">My Unapproved Overrun Minutes</p>
                        <p
                            class="text-base font-semibold {{ $myUnapprovedOverrunMinutes > 0 ? 'text-red-700' : 'text-gray-800' }} mt-1">
                            {{ $myUnapprovedOverrunMinutes }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Task Total Consumed Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $taskConsumedMinutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Task Remaining Allowed Minutes</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $taskRemainingAllowedMinutes }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Task Unapproved Overrun Minutes</p>
                        <p
                            class="text-base font-semibold {{ $taskUnapprovedOverrunMinutes > 0 ? 'text-red-700' : 'text-gray-800' }} mt-1">
                            {{ $taskUnapprovedOverrunMinutes }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Completed At</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">
                            {{ $task->completed_at?->format('Y-m-d h:i A') ?? '-' }}
                        </p>
                    </div>

                    @if(isset($myRunningLog) && $myRunningLog)
                        <div class="bg-green-50 rounded-lg p-4 md:col-span-2">
                            <p class="text-sm text-green-700">Current Running Session</p>
                            <p class="text-lg font-bold text-green-800 mt-1" x-text="formatted"></p>
                        </div>
                    @endif

                    @if($myUnapprovedOverrunMinutes > 0)
                        <div class="bg-red-50 rounded-lg p-4 md:col-span-2">
                            <p class="text-sm text-red-700 font-semibold">Warning</p>
                            <p class="text-base text-red-800 mt-1">
                                You have exceeded your currently allowed time by {{ $myUnapprovedOverrunMinutes }} minutes.
                                Request extension approval to regularize this extra time.
                            </p>
                        </div>
                    @endif

                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $task->description ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @can('request task extension')
                @if($task->assignedUsers->pluck('id')->contains(auth()->id()) && $task->status !== 'completed')
                    <div class="bg-white shadow rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Request Time Extension</h3>

                        @if($myPendingExtensionRequest)
                            <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                                You already have a pending extension request for this task.
                            </div>
                        @else
                            <form action="{{ route('tasks.extension-requests.store', $task) }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Requested Extra Minutes</label>
                                    <input type="number" name="requested_extra_minutes" min="1"
                                        class="w-full rounded-lg border-gray-300" placeholder="e.g. 60">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea name="reason" rows="4" class="w-full rounded-lg border-gray-300"
                                        placeholder="Please explain why you need more time."></textarea>
                                </div>

                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                                    Submit Extension Request
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endcan

            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Extension Requests</h3>

                @if($task->extensionRequests->count())
                    <div class="space-y-4">
                        @foreach($task->extensionRequests->sortByDesc('created_at') as $extensionRequest)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $extensionRequest->user->name ?? '-' }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Requested: {{ $extensionRequest->requested_extra_minutes }} minutes
                                        </p>
                                        <p class="text-sm text-gray-700 mt-2">
                                            {{ $extensionRequest->reason }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Submitted at: {{ $extensionRequest->created_at?->format('Y-m-d h:i A') }}
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                                                                                @if($extensionRequest->status === 'approved') bg-green-100 text-green-700
                                                                                                @elseif($extensionRequest->status === 'rejected') bg-red-100 text-red-700
                                                                                                @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst($extensionRequest->status) }}
                                    </span>
                                </div>

                                @if($extensionRequest->review_note)
                                    <div class="mt-3 text-sm text-gray-700">
                                        <span class="font-semibold">Review Note:</span>
                                        {{ $extensionRequest->review_note }}
                                    </div>
                                @endif

                                @if($extensionRequest->reviewer)
                                    <div class="mt-2 text-xs text-gray-500">
                                        Reviewed by {{ $extensionRequest->reviewer->name }}
                                        on {{ $extensionRequest->reviewed_at?->format('Y-m-d h:i A') }}
                                    </div>
                                @endif

                                @can('approve task extension')
                                    @if($extensionRequest->status === 'pending')
                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <form action="{{ route('task-extension-requests.approve', $extensionRequest) }}"
                                                method="POST" class="space-y-3">
                                                @csrf

                                                <textarea name="review_note" rows="3" class="w-full rounded-lg border-gray-300"
                                                    placeholder="Approval note (optional)"></textarea>

                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('task-extension-requests.reject', $extensionRequest) }}"
                                                method="POST" class="space-y-3">
                                                @csrf

                                                <textarea name="review_note" rows="3" class="w-full rounded-lg border-gray-300"
                                                    placeholder="Rejection note (optional)"></textarea>

                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No extension requests found for this task.
                    </div>
                @endif
            </div>
            <div class="bg-white shadow rounded-xl p-6" x-data="mentionSystem(@json($task->assignedUsers))">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Task Discussion</h3>

                @can('comment on tasks')
                                    <div class="mb-6">
                                        <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="space-y-4">
                                            @csrf

                                            <div class="relative">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Add Comment</label>
                                                <textarea name="comment" rows="4" x-model="comment" @input="handleMention"
                                                    @keydown.escape="showSuggestions = false" class="w-full rounded-lg border-gray-300"
                                                    placeholder="Write a comment... Use @name to mention assigned teammates.">
                                                </textarea>
                                                <div x-show="showSuggestions" @click.away="showSuggestions = false"
                                                    class="border rounded-lg bg-white shadow mt-1 max-h-40 overflow-y-auto absolute z-50 w-full">

                                                    <template x-for="user in filteredUsers" :key="user.id">
                                                        <div @click="selectUser(user)"
                                                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm flex justify-between">

                                                            <span x-text="user.name"></span>
                                                            <span class="text-xs text-gray-500" x-text="user.email"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                                @error('comment')
                                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                                Post Comment
                                            </button>
                                        </form>

                                        <p class="text-xs text-gray-500 mt-2">
                                            Mention format example: @Ali
                                        </p>
                                    </div>
                @endcan

                @if($task->comments->count())
                    <div class="space-y-4">
                        @foreach($task->comments as $comment)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $comment->user->name ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $comment->created_at?->format('Y-m-d h:i A') ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $comment->comment }}
                                </div>

                                @if($comment->mentionedUsers->count())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($comment->mentionedUsers as $mentionedUser)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                @{{ $mentionedUser->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No comments yet.
                    </div>
                @endif
            </div>
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Task Attachments</h3>

                @can('upload task attachments')
                    <div class="mb-6">
                        <form action="{{ route('tasks.attachments.store', $task) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Files</label>
                                <input type="file" name="attachments[]" multiple class="w-full rounded-lg border-gray-300">
                                @error('attachments')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                @error('attachments.*')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">
                                    Allowed: jpg, png, webp, pdf, doc, docx, xls, xlsx, csv, zip, rar, txt. Max 10 MB each.
                                </p>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Upload Attachment(s)
                            </button>
                        </form>
                    </div>
                @endcan

                @if($task->attachments->count())
                    <div class="space-y-3">
                        @foreach($task->attachments as $attachment)
                            <div
                                class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $attachment->original_name }}</p>
                                    <div class="mt-1 text-sm text-gray-500 space-y-1">
                                        <p>Uploaded by: {{ $attachment->uploader->name ?? '-' }}</p>
                                        <p>Size: {{ $attachment->formatted_file_size }}</p>
                                        <p>Uploaded at: {{ $attachment->created_at?->format('Y-m-d h:i A') ?? '-' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ $attachment->file_url }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Open
                                    </a>

                                    <a href="{{ $attachment->file_url }}" download="{{ $attachment->original_name }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No attachments uploaded yet.
                    </div>
                @endif
            </div>
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Time Logs</h3>

                @if($task->timeLogs->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Started At</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Stopped At</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Minutes</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($task->timeLogs->sortByDesc('started_at') as $log)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $log->user->name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $log->started_at?->format('Y-m-d h:i A') ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $log->stopped_at?->format('Y-m-d h:i A') ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $log->duration_minutes }}</td>
                                        <td class="px-4 py-3">
                                            {{ $log->is_running ? 'Running' : 'Stopped' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No time logs found for this task.
                    </div>
                @endif
            </div>
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Activity Log</h3>

                @if($task->activityLogs->count())
                    <div class="space-y-3">
                        @foreach($task->activityLogs as $activity)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $activity->user->name ?? 'System' }}
                                        </p>
                                        <p class="text-sm text-gray-700 mt-1">
                                            {{ $activity->description ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Action: {{ $activity->action }}
                                        </p>
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $activity->created_at?->format('Y-m-d h:i A') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No activity logs found for this task.
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

        function mentionSystem(users) {
            return {
                comment: '',
                users: users,
                showSuggestions: false,
                filteredUsers: [],

                handleMention() {
                    const text = this.comment;
                    const match = text.match(/@(\w*)$/);

                    if (match) {
                        const keyword = match[1].toLowerCase();

                        this.filteredUsers = this.users.filter(user =>
                            user.name.toLowerCase().includes(keyword)
                        );

                        this.showSuggestions = this.filteredUsers.length > 0;
                    } else {
                        this.showSuggestions = false;
                    }
                },

                selectUser(user) {
                    this.comment = this.comment.replace(/@(\w*)$/, '@' + user.name + ' ');
                    this.showSuggestions = false;
                }
            }
        }

    </script>
</x-app-layout>