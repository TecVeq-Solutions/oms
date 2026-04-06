<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">My Attendance</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Track your attendance, selfies, location, late minutes, overtime and break time.
                    </p>
                </div>

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

                <div class="space-y-6">
                    @forelse($attendances as $attendance)
                        <div class="border border-gray-200 rounded-2xl p-5 shadow-sm bg-white">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $attendance->attendance_date->format('Y-m-d') }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Shift: {{ $attendance->shift?->name ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($attendance->status === 'late')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                            Late
                                        </span>
                                    @elseif($attendance->status === 'present')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                            Present
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    @endif

                                    @if($attendance->is_suspicious)
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                            Suspicious
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
                                <div class="xl:col-span-2 space-y-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Check-In</h4>

                                            <div class="flex items-center gap-4 mb-4">
                                                @if($attendance->photo_path)
                                                    <img
                                                        src="{{ asset('storage/' . $attendance->photo_path) }}"
                                                        alt="Check-in selfie"
                                                        class="w-16 h-16 rounded-full object-cover border border-gray-200 shadow-sm"
                                                    >
                                                @else
                                                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                                        N/A
                                                    </div>
                                                @endif

                                                <div>
                                                    <p class="text-sm text-gray-500">Time</p>
                                                    <p class="text-base font-semibold text-gray-800">
                                                        {{ $attendance->check_in ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="space-y-2 text-sm text-gray-700">
                                                <div>
                                                    <span class="font-medium text-gray-800">Location:</span>
                                                    @if($attendance->latitude && $attendance->longitude)
                                                        <div class="mt-1">
                                                            {{ number_format((float) $attendance->latitude, 6) }},
                                                            {{ number_format((float) $attendance->longitude, 6) }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400"> No location</span>
                                                    @endif
                                                </div>

                                                <div>
                                                    <span class="font-medium text-gray-800">Distance:</span>
                                                    {{ number_format((float) ($attendance->distance_from_office ?? 0), 2) }} m
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Check-Out</h4>

                                            <div class="flex items-center gap-4 mb-4">
                                                @if(!empty($attendance->checkout_photo_path))
                                                    <img
                                                        src="{{ asset('storage/' . $attendance->checkout_photo_path) }}"
                                                        alt="Check-out selfie"
                                                        class="w-16 h-16 rounded-full object-cover border border-gray-200 shadow-sm"
                                                    >
                                                @else
                                                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                                        N/A
                                                    </div>
                                                @endif

                                                <div>
                                                    <p class="text-sm text-gray-500">Time</p>
                                                    <p class="text-base font-semibold text-gray-800">
                                                        {{ $attendance->check_out ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="space-y-2 text-sm text-gray-700">
                                                <div>
                                                    <span class="font-medium text-gray-800">Location:</span>
                                                    @if(!empty($attendance->checkout_latitude) && !empty($attendance->checkout_longitude))
                                                        <div class="mt-1">
                                                            {{ number_format((float) $attendance->checkout_latitude, 6) }},
                                                            {{ number_format((float) $attendance->checkout_longitude, 6) }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400"> No location</span>
                                                    @endif
                                                </div>

                                                <div>
                                                    <span class="font-medium text-gray-800">Distance:</span>
                                                    {{ number_format((float) ($attendance->checkout_distance_from_office ?? 0), 2) }} m
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="rounded-xl bg-blue-50 p-4">
                                            <p class="text-xs font-medium text-blue-600 uppercase">Late</p>
                                            <p class="mt-2 text-lg font-semibold text-gray-800">
                                                {{ $attendance->late_minutes ?? 0 }} <span class="text-sm font-normal">min</span>
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-green-50 p-4">
                                            <p class="text-xs font-medium text-green-600 uppercase">Overtime</p>
                                            <p class="mt-2 text-lg font-semibold text-gray-800">
                                                {{ $attendance->overtime_minutes ?? 0 }} <span class="text-sm font-normal">min</span>
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-yellow-50 p-4">
                                            <p class="text-xs font-medium text-yellow-600 uppercase">Break</p>
                                            <p class="mt-2 text-lg font-semibold text-gray-800">
                                                {{ $attendance->break_minutes ?? 0 }} <span class="text-sm font-normal">min</span>
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-purple-50 p-4">
                                            <p class="text-xs font-medium text-purple-600 uppercase">Worked</p>
                                            <p class="mt-2 text-lg font-semibold text-gray-800">
                                                {{ $attendance->worked_minutes ?? 0 }} <span class="text-sm font-normal">min</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Notes</h4>

                                        <div class="space-y-3 text-sm text-gray-700">
                                            @if($attendance->privacy_note)
                                                <div class="rounded-lg bg-white p-3 border border-gray-100">
                                                    <span class="font-semibold text-gray-800">Check-In:</span>
                                                    <p class="mt-1 text-gray-600">
                                                        {{ $attendance->privacy_note }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if(!empty($attendance->checkout_privacy_note))
                                                <div class="rounded-lg bg-white p-3 border border-gray-100">
                                                    <span class="font-semibold text-gray-800">Check-Out:</span>
                                                    <p class="mt-1 text-gray-600">
                                                        {{ $attendance->checkout_privacy_note }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if($attendance->suspicious_reason)
                                                <div class="rounded-lg bg-red-50 p-3 border border-red-100 text-red-700">
                                                    <span class="font-semibold">Reason:</span>
                                                    <p class="mt-1">
                                                        {{ $attendance->suspicious_reason }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if(!$attendance->privacy_note && empty($attendance->checkout_privacy_note) && !$attendance->suspicious_reason)
                                                <p class="text-gray-400">No notes available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-500">
                            No attendance records found.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>