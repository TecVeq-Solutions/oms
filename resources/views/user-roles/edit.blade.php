<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Manage Role & Shift</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Update user role and assign shift.
                </p>

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">User Name</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $user->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $user->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('user-roles.update', $user) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
                        <select name="roles[]" id="roles-select" multiple
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Shift</label>
                        <select name="shift_id"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">No Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}"
                                    {{ (string) old('shift_id', optional($user->employee)->shift_id) === (string) $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }}
                                    ({{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                    - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }})
                                </option>
                            @endforeach
                        </select>

                        @if(!$user->employee)
                            <p class="text-xs text-red-500 mt-1">
                                This user has no employee profile, so shift cannot be assigned yet.
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Save Changes
                        </button>

                        <a href="{{ route('user-roles.index') }}"
                           class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--multiple {
                border-color: #d1d5db;
                border-radius: 0.5rem;
                min-height: 42px;
                padding: 2px 8px;
            }
            .select2-container--default.select2-container--focus .select2-selection--multiple {
                border-color: #6366f1;
                box-shadow: 0 0 0 1px #6366f1;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#roles-select').select2({
                    placeholder: "Select roles",
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endpush
</x-app-layout>