<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="bg-white shadow rounded-xl p-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit My Profile</h2>
                    <p class="text-sm text-gray-500 mt-1">Update your contact, address, emergency contact and documents.</p>
                </div>
                <a href="{{ route('profile.employee') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
                    ← Back
                </a>
            </div>

            {{-- Flash / Errors --}}
            @if(session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.employee.update') }}" enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Read-only Info Notice --}}
                <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-4 py-3 text-sm">
                    ℹ️ Name, email, CNIC, department and designation can only be updated by admin. You can edit contact details, addresses, emergency contact and upload documents.
                </div>

                {{-- Contact Info --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Contact Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" value="{{ $employee->full_name }}" disabled
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-400 text-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="{{ $employee->email }}" disabled
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-400 text-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="phone" name="phone"
                                   value="{{ old('phone', $employee->phone) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                                   placeholder="+92 300 0000000">
                        </div>

                    </div>
                </div>

                {{-- Address --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Address</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div class="sm:col-span-2">
                            <label for="current_address" class="block text-sm font-medium text-gray-700 mb-1">Current Address</label>
                            <textarea id="current_address" name="current_address" rows="2"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">{{ old('current_address', $personal?->current_address) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="permanent_address" class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                            <textarea id="permanent_address" name="permanent_address" rows="2"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">{{ old('permanent_address', $personal?->permanent_address) }}</textarea>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" id="city" name="city"
                                   value="{{ old('city', $personal?->city) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" id="country" name="country"
                                   value="{{ old('country', $personal?->country) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Emergency Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                   value="{{ old('emergency_contact_name', $personal?->emergency_contact_name) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                   value="{{ old('emergency_contact_phone', $personal?->emergency_contact_phone) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                        <div>
                            <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-1">Relation</label>
                            <input type="text" id="emergency_contact_relation" name="emergency_contact_relation"
                                   value="{{ old('emergency_contact_relation', $personal?->emergency_contact_relation) }}"
                                   placeholder="e.g. Father, Wife"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>

                    </div>
                </div>

                {{-- Documents --}}
                <div class="bg-white shadow rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-1 border-b pb-2">Documents</h3>
                    <p class="text-xs text-gray-400 mb-4">Accepted: JPG, PNG, PDF — max 4MB each. Uploading a new file will reset its status to Pending Review.</p>

                    @php
                        $docList = [
                            'cnic_front_photo' => ['label' => 'CNIC Front', 'status' => 'cnic_front_status', 'reject' => 'cnic_front_reject_reason'],
                            'cnic_back_photo'  => ['label' => 'CNIC Back',  'status' => 'cnic_back_status',  'reject' => 'cnic_back_reject_reason'],
                            'document_1'       => ['label' => 'Document 1', 'status' => 'document_1_status', 'reject' => 'document_1_reject_reason'],
                            'document_2'       => ['label' => 'Document 2', 'status' => 'document_2_status', 'reject' => 'document_2_reject_reason'],
                            'document_3'       => ['label' => 'Document 3', 'status' => 'document_3_status', 'reject' => 'document_3_reject_reason'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        @foreach($docList as $field => $meta)
                            @php
                                $existing  = $personal?->$field;
                                $status    = $personal?->{$meta['status']};
                                $rejectMsg = $personal?->{$meta['reject']};
                            @endphp
                            <div class="rounded-lg border border-gray-200 p-4 space-y-2">
                                <p class="text-sm font-medium text-gray-700">{{ $meta['label'] }}</p>

                                @if($existing)
                                    <a href="{{ asset('storage/' . $existing) }}" target="_blank"
                                       class="text-xs text-indigo-600 hover:underline">View current file</a>

                                    @if($status === 'verified')
                                        <span class="block text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded-full w-fit">✅ Verified</span>
                                    @elseif($status === 'rejected')
                                        <span class="block text-xs text-red-700 bg-red-100 px-2 py-0.5 rounded-full w-fit">❌ Rejected</span>
                                        @if($rejectMsg)
                                            <p class="text-xs text-red-600">{{ $rejectMsg }}</p>
                                        @endif
                                    @elseif($status === 'pending')
                                        <span class="block text-xs text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full w-fit">⏳ Pending</span>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic">Not uploaded yet</p>
                                @endif

                                <label class="block">
                                    <span class="block text-xs text-gray-500 mb-1">{{ $existing ? 'Replace file' : 'Upload file' }}</span>
                                    <input type="file" name="{{ $field }}" id="{{ $field }}"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           class="block w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                        Save Changes
                    </button>
                    <a href="{{ route('profile.employee') }}"
                       class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
