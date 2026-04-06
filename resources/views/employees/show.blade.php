<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $personal = $employee->personalDetail;
            @endphp

            <!-- Header -->
            <div class="bg-white shadow rounded-xl p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div>
                        @if($personal?->profile_photo)
                            <img src="{{ asset('storage/' . $personal->profile_photo) }}"
                                 class="w-20 h-20 rounded-full object-cover border">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                N/A
                            </div>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $employee->full_name }}</h2>
                        <p class="text-sm text-gray-500">Employee Profile</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $employee->employee_code }}</p>
                    </div>
                </div>

                <a href="{{ route('employees.edit', $employee) }}"
                   class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                    Edit
                </a>
            </div>

            <!-- Basic Info -->
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-semibold">{{ $employee->email }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-semibold">{{ $employee->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="font-semibold">{{ $employee->department ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Designation</p>
                        <p class="font-semibold">{{ $employee->designation ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Joining Date</p>
                        <p class="font-semibold">
                            {{ $employee->joining_date?->format('Y-m-d') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @if($employee->status === 'active')
                            <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-4">Personal Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Father Name</p>
                        <p class="font-semibold">{{ $personal?->father_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">CNIC</p>
                        <p class="font-semibold">{{ $personal?->cnic_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Date of Birth</p>
                        <p class="font-semibold">
                            {{ $personal?->date_of_birth?->format('Y-m-d') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Gender</p>
                        <p class="font-semibold">{{ ucfirst($personal?->gender ?? '-') }}</p>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-4">Address</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Current Address</p>
                        <p class="font-semibold">{{ $personal?->current_address ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Permanent Address</p>
                        <p class="font-semibold">{{ $personal?->permanent_address ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">City</p>
                        <p class="font-semibold">{{ $personal?->city ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Country</p>
                        <p class="font-semibold">{{ $personal?->country ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Emergency -->
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-4">Emergency Contact</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-semibold">{{ $personal?->emergency_contact_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-semibold">{{ $personal?->emergency_contact_phone ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Relation</p>
                        <p class="font-semibold">{{ $personal?->emergency_contact_relation ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-4">Documents</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div>
                        <p class="text-sm text-gray-500 mb-2">CNIC Front</p>
                        @if($personal?->cnic_front_photo)
                            <a href="{{ asset('storage/' . $personal->cnic_front_photo) }}" target="_blank"
                               class="text-indigo-600 underline">View</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-2">CNIC Back</p>
                        @if($personal?->cnic_back_photo)
                            <a href="{{ asset('storage/' . $personal->cnic_back_photo) }}" target="_blank"
                               class="text-indigo-600 underline">View</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-2">Document 1</p>
                        @if($personal?->document_1)
                            <a href="{{ asset('storage/' . $personal->document_1) }}" target="_blank"
                               class="text-indigo-600 underline">View</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-2">Document 2</p>
                        @if($personal?->document_2)
                            <a href="{{ asset('storage/' . $personal->document_2) }}" target="_blank"
                               class="text-indigo-600 underline">View</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 mb-2">Document 3</p>
                        @if($personal?->document_3)
                            <a href="{{ asset('storage/' . $personal->document_3) }}" target="_blank"
                               class="text-indigo-600 underline">View</a>
                        @else
                            <span>-</span>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Back -->
            <div>
                <a href="{{ route('employees.index') }}"
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Back
                </a>
            </div>

        </div>
    </div>
</x-app-layout>