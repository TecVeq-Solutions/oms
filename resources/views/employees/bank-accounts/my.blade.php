<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800">My Bank Account</h2>
                <p class="text-sm text-gray-500 mt-1">
                    View your bank account details
                </p>
            </div>

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
                    </div>
                </div>
            @else
                <div class="bg-yellow-100 text-yellow-800 rounded-xl px-4 py-3 shadow">
                    No bank account found.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>