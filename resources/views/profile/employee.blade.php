<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $personal = $employee?->personalDetail;

                $docList = [
                    'cnic_front_photo' => ['label' => 'CNIC Front',  'status' => 'cnic_front_status', 'reject' => 'cnic_front_reject_reason'],
                    'cnic_back_photo'  => ['label' => 'CNIC Back',   'status' => 'cnic_back_status',  'reject' => 'cnic_back_reject_reason'],
                    'document_1'       => ['label' => 'Document 1',  'status' => 'document_1_status', 'reject' => 'document_1_reject_reason'],
                    'document_2'       => ['label' => 'Document 2',  'status' => 'document_2_status', 'reject' => 'document_2_reject_reason'],
                    'document_3'       => ['label' => 'Document 3',  'status' => 'document_3_status', 'reject' => 'document_3_reject_reason'],
                ];
            @endphp

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
            @endif

            @if($employee)

                {{-- Header --}}
                <div class="bg-white shadow rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if($personal?->profile_photo)
                            <img src="{{ asset('storage/' . $personal->profile_photo) }}"
                                 class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200">
                        @else
                            <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-2xl font-bold">
                                {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $employee->full_name }}</h2>
                            <p class="text-sm text-gray-500">{{ $employee->employee_code }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                {{ $employee->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('profile.employee.edit') }}"
                       class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                        ✏️ Edit Profile
                    </a>
                </div>

                {{-- Basic Info --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Basic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Department</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->department ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Designation</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->designation ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Joining Date</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->joining_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Shift</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $employee->shift?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Personal Details --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Personal Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Father Name</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->father_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">CNIC</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->cnic_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Date of Birth</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->date_of_birth?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Gender</p>
                            <p class="font-medium text-gray-800 mt-1">{{ ucfirst($personal?->gender ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">City</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Country</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->country ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Current Address</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->current_address ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Permanent Address</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->permanent_address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Emergency Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Name</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->emergency_contact_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->emergency_contact_phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Relation</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $personal?->emergency_contact_relation ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <h3 class="text-base font-semibold text-gray-700">Documents</h3>
                        <a href="{{ route('profile.employee.edit') }}"
                           class="text-sm text-indigo-600 hover:underline">Upload / Update Docs</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        @foreach($docList as $field => $meta)
                            @php
                                $filePath  = $personal?->$field;
                                $status    = $personal?->{$meta['status']};
                                $rejectMsg = $personal?->{$meta['reject']};
                            @endphp
                            <div class="rounded-lg border p-4 flex flex-col gap-2">
                                <p class="text-sm font-medium text-gray-700">{{ $meta['label'] }}</p>

                                @if($filePath)
                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                       class="text-indigo-600 text-sm hover:underline truncate">View File</a>

                                    @if($status === 'verified')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full w-fit">
                                            ✅ Verified
                                        </span>
                                    @elseif($status === 'rejected')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-100 px-2 py-0.5 rounded-full w-fit">
                                            ❌ Rejected
                                        </span>
                                        @if($rejectMsg)
                                            <p class="text-xs text-red-600">Reason: {{ $rejectMsg }}</p>
                                        @endif
                                    @elseif($status === 'pending')
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full w-fit">
                                            ⏳ Pending Review
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Not reviewed yet</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Not uploaded</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            @else
                <div class="bg-white shadow rounded-xl p-6">
                    <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        Your employee profile is not linked yet. Please contact admin.
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>