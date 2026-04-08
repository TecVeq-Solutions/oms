<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Employee Bank Account</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $employee->full_name ?? $employee->name ?? 'Employee' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('employees.bank-account.edit', $employee) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Edit Bank Account
                        </a>

                        <a href="{{ route('employees.salary-payments.index', $employee) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            View Salary History
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 rounded-xl px-4 py-3 shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if($bankAccount)
                <div class="bg-white shadow rounded-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-sm text-gray-500">Bank Name</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->bank_name }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Account Title</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->account_title }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Account Number</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->account_number }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">IBAN</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->iban ?: '-' }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Branch Name</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->branch_name ?: '-' }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Branch Code</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">{{ $bankAccount->branch_code ?: '-' }}</h3>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <div class="mt-1">
                                @if($bankAccount->is_active)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Created At</p>
                            <h3 class="text-base font-semibold text-gray-800 mt-1">
                                {{ $bankAccount->created_at?->format('Y-m-d h:i A') ?? '-' }}
                            </h3>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-100 text-yellow-800 rounded-xl px-4 py-3 shadow">
                    No bank account has been added for this employee yet.
                </div>

                <div class="bg-white shadow rounded-xl p-6">
                    <a href="{{ route('employees.bank-account.edit', $employee) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Add Bank Account
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>