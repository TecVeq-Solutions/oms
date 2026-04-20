<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Task Extension Requests</h2>

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

                @if($requests->count())
                    <div class="space-y-4">
                        @foreach($requests as $requestItem)
                            <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Task</p>
                                        <p class="font-semibold text-gray-800">{{ $requestItem->task->title ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Workspace / Project</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $requestItem->task->project->workspace->name ?? '-' }} /
                                            {{ $requestItem->task->project->name ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Requested By</p>
                                        <p class="font-semibold text-gray-800">{{ $requestItem->user->name ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Requested Minutes</p>
                                        <p class="font-semibold text-gray-800">{{ $requestItem->requested_extra_minutes }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Task Allowed Minutes</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ ($requestItem->task->estimated_minutes ?? 0) + ($requestItem->task->approved_extra_minutes ?? 0) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Task Consumed Minutes</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $requestItem->task->timeLogs()->sum('duration_minutes') }}
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <p class="text-sm text-gray-500">Reason</p>
                                        <p class="font-semibold text-gray-800">{{ $requestItem->reason }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Status</p>
                                        <p class="font-semibold text-gray-800 capitalize">{{ $requestItem->status }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">Submitted At</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $requestItem->created_at?->format('Y-m-d h:i A') ?? '-' }}</p>
                                    </div>


                                    @if($requestItem->reviewer)
                                        <div>
                                            <p class="text-sm text-gray-500">Reviewed By</p>
                                            <p class="font-semibold text-gray-800">{{ $requestItem->reviewer->name }}</p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-500">Reviewed At</p>
                                            <p class="font-semibold text-gray-800">
                                                {{ $requestItem->reviewed_at?->format('Y-m-d h:i A') ?? '-' }}</p>
                                        </div>
                                    @endif

                                    @if($requestItem->review_note)
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500">Review Note</p>
                                            <p class="font-semibold text-gray-800">{{ $requestItem->review_note }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($requestItem->status === 'pending')
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <form action="{{ route('task-extension-requests.approve', $requestItem) }}" method="POST"
                                            class="space-y-3">
                                            @csrf

                                            <textarea name="review_note" rows="3" class="w-full rounded-lg border-gray-300"
                                                placeholder="Approval note (optional)"></textarea>

                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('task-extension-requests.reject', $requestItem) }}" method="POST"
                                            class="space-y-3">
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
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No task extension requests found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>