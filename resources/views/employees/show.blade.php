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
                <h3 class="text-lg font-semibold mb-4">Documents & Verification</h3>

                @php
                    $docList = [
                        'cnic_front_photo' => ['label' => 'CNIC Front', 'status' => 'cnic_front_status', 'reject' => 'cnic_front_reject_reason'],
                        'cnic_back_photo'  => ['label' => 'CNIC Back',  'status' => 'cnic_back_status',  'reject' => 'cnic_back_reject_reason'],
                        'document_1'       => ['label' => 'Document 1', 'status' => 'document_1_status', 'reject' => 'document_1_reject_reason'],
                        'document_2'       => ['label' => 'Document 2', 'status' => 'document_2_status', 'reject' => 'document_2_reject_reason'],
                        'document_3'       => ['label' => 'Document 3', 'status' => 'document_3_status', 'reject' => 'document_3_reject_reason'],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($docList as $field => $meta)
                        @php
                            $filePath = $personal?->$field;
                            $status   = $personal?->{$meta['status']};
                            $reason   = $personal?->{$meta['reject']};
                        @endphp
                        <div class="border rounded-xl p-4 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-sm font-medium text-gray-700">{{ $meta['label'] }}</p>
                                    @if($status === 'verified')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-700 rounded-full">VERIFIED</span>
                                    @elseif($status === 'rejected')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-700 rounded-full">REJECTED</span>
                                    @elseif($status === 'pending')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-yellow-100 text-yellow-700 rounded-full animate-pulse">PENDING</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full">NOT UPLOADED</span>
                                    @endif
                                </div>

                                @if($filePath)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank" 
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Document
                                        </a>
                                    </div>
                                    @if($status === 'rejected' && $reason)
                                        <p class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded"><strong>Reason:</strong> {{ $reason }}</p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic mt-2 text-center py-4 bg-gray-50 rounded-lg">No file uploaded</p>
                                @endif
                            </div>

                            @if($filePath && auth()->user()->can('edit employees'))
                                <div class="flex gap-2 pt-2 border-t mt-2">
                                    @if($status !== 'verified')
                                        <form action="{{ route('employees.docs.verify', [$employee, $field]) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition">
                                                Verify
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button type="button" 
                                            onclick="openRejectModal('{{ $field }}', '{{ $meta['label'] }}')"
                                            class="flex-1 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-lg transition">
                                        {{ $status === 'rejected' ? 'Update Rejection' : 'Reject' }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Rejection Modal -->
            <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <form id="rejectForm" method="POST" action="">
                            @csrf
                            <div>
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Reject <span id="modalDocLabel"></span>
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Please provide a reason for rejecting this document. The employee will see this reason.
                                        </p>
                                        <textarea name="reject_reason" id="reject_reason" rows="4" required
                                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                  placeholder="e.g. Document is blurred or expired..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openRejectModal(field, label) {
                    const form = document.getElementById('rejectForm');
                    const modal = document.getElementById('rejectModal');
                    const docLabel = document.getElementById('modalDocLabel');
                    
                    docLabel.textContent = label;
                    form.action = `/employees/{{ $employee->id }}/docs/${field}/reject`;
                    modal.classList.remove('hidden');
                }

                function closeRejectModal() {
                    const modal = document.getElementById('rejectModal');
                    modal.classList.add('hidden');
                }
            </script>

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