<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tracking: ') . $employee->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filter & Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 flex justify-between items-center">
                <div>
                    <form method="GET" class="flex space-x-2">
                        <input type="date" name="date" value="{{ $date }}" class="border rounded p-2" onchange="this.form.submit()">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
                    </form>
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('admin.tracking.toggle', $employee->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded text-white {{ $employee->is_tracked ? 'bg-red-500' : 'bg-green-500' }}">
                            {{ $employee->is_tracked ? 'Disable Tracking' : 'Enable Tracking' }}
                        </button>
                    </form>
                    @if($employee->is_tracked)
                    <form action="{{ route('admin.tracking.regenerate-token', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure? Existing client will disconnect.');">
                        @csrf
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Regenerate API Token</button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- API Token Box -->
            @if($employee->is_tracked && $employee->tracking_api_token)
            <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 flex justify-between items-center">
                <div>
                    <span class="font-bold">API Token for Client:</span>
                    <code class="ml-2 bg-blue-100 p-1 rounded">{{ $employee->tracking_api_token }}</code>
                </div>
            </div>
            @endif

            <!-- Screenshots Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Screenshots for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }} ({{ $screenshots->total() }})</h3>
                
                @if($screenshots->isEmpty())
                    <div class="text-center text-gray-500 py-8">No screenshots captured on this date.</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                        @foreach($screenshots as $s)
                        <div class="border rounded shadow-sm relative group overflow-hidden">
                            <a href="/storage/{{ $s->file_path }}" target="_blank">
                                <img src="/storage/{{ $s->file_path }}" class="w-full h-40 object-cover" loading="lazy">
                            </a>
                            <div class="p-2 text-xs bg-white">
                                <div class="flex justify-between">
                                    <span class="font-bold">{{ $s->captured_at->format('h:i:s A') }}</span>
                                    @if($s->attendance_id)
                                    <span class="text-blue-500" title="Linked to Attendance">🔗</span>
                                    @endif
                                </div>
                                <div class="truncate text-gray-500 mt-1" title="{{ $s->active_window_title }}">
                                    🪟 {{ $s->active_window_title ?: 'Unknown' }}
                                </div>
                                <div class="truncate text-blue-500">
                                    ⚙️ {{ $s->active_process_name ?: 'Unknown' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{ $screenshots->appends(['date' => $date])->links() }}
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
