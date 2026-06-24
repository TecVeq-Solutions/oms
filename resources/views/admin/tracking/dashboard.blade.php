<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Screenshot Tracking Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Tracked Employees</div>
                    <div class="text-2xl font-bold">{{ $totalTracked }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Online Now</div>
                    <div class="text-2xl font-bold text-green-600">{{ $onlineNow }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Today's Screenshots</div>
                    <div class="text-2xl font-bold">{{ $todayScreenshots }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Interval</div>
                    <div class="text-2xl font-bold">{{ $interval }} mins</div>
                </div>
            </div>

            <!-- Tracked Employees Status Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">Employee Status</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="employee-status-grid">
                    @foreach($trackedEmployees as $emp)
                    <a href="{{ route('admin.tracking.employee', $emp->id) }}" class="block border rounded p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold">{{ $emp->full_name }}</span>
                            @if($emp->is_online)
                                <span class="h-3 w-3 rounded-full bg-green-500 inline-block" title="Online"></span>
                            @else
                                <span class="h-3 w-3 rounded-full bg-gray-400 inline-block" title="Offline"></span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">
                            Last Active: {{ $emp->last_tracking_heartbeat ? $emp->last_tracking_heartbeat->diffForHumans() : 'Never' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Latest Screenshot: {{ $emp->latestScreenshot ? $emp->latestScreenshot->captured_at->diffForHumans() : 'N/A' }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Recent Screenshots AJAX Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Recent Screenshots</h3>
                    <div class="flex space-x-2">
                        <select id="filter-employee" class="border rounded p-1 text-sm">
                            <option value="">All Employees</option>
                            @foreach($trackedEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="filter-date" class="border rounded p-1 text-sm" value="{{ now()->toDateString() }}">
                        <button onclick="loadScreenshots()" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">Refresh</button>
                    </div>
                </div>
                
                <div id="screenshots-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Loaded via AJAX -->
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function loadScreenshots() {
            const empId = document.getElementById('filter-employee').value;
            const date = document.getElementById('filter-date').value;
            
            fetch(`/admin/tracking/api/screenshots?employee_id=${empId}&date=${date}`)
                .then(res => res.json())
                .then(data => {
                    const grid = document.getElementById('screenshots-grid');
                    grid.innerHTML = '';
                    if(data.data.length === 0) {
                        grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-4">No screenshots found.</div>';
                        return;
                    }
                    data.data.forEach(s => {
                        grid.innerHTML += `
                            <div class="border rounded overflow-hidden shadow-sm relative group">
                                <img src="/storage/${s.file_path}" class="w-full h-40 object-cover lazyload" alt="Screenshot" loading="lazy">
                                <div class="p-2 text-xs bg-white">
                                    <div class="font-bold truncate">${s.employee.full_name}</div>
                                    <div class="text-gray-500">${new Date(s.captured_at).toLocaleTimeString()}</div>
                                    <div class="truncate text-gray-400 mt-1" title="${s.active_window_title || 'Unknown Window'}">
                                        🪟 ${s.active_window_title || 'Unknown Window'}
                                    </div>
                                    <div class="truncate text-blue-500">
                                        ⚙️ ${s.active_process_name || 'Unknown'}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                });
        }

        function updateStatus() {
            fetch(`/admin/tracking/api/status`)
                .then(res => res.json())
                .then(data => {
                    // Update dots silently
                    const grid = document.getElementById('employee-status-grid');
                    // Logic to update status can be expanded. Reloading is simpler for now.
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadScreenshots();
            setInterval(loadScreenshots, 5 * 60 * 1000); // 5 min refresh
            setInterval(updateStatus, 60 * 1000); // 1 min heartbeat update
        });
    </script>
    @endpush
</x-app-layout>
