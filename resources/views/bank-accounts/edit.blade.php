<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Bank Account</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $employee->full_name ?? $employee->name ?? 'Employee' }}
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-800 rounded-xl px-4 py-3 shadow">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-xl p-6">
                <form action="{{ route('employees.bank-account.update', $employee) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                            <input type="text" name="bank_name"
                                   value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Title</label>
                            <input type="text" name="account_title"
                                   value="{{ old('account_title', $bankAccount->account_title ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                            <input type="text" name="account_number"
                                   value="{{ old('account_number', $bankAccount->account_number ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
                            <input type="text" name="iban"
                                   value="{{ old('iban', $bankAccount->iban ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
                            <input type="text" name="branch_name"
                                   value="{{ old('branch_name', $bankAccount->branch_name ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Branch Code</label>
                            <input type="text" name="branch_code"
                                   value="{{ old('branch_code', $bankAccount->branch_code ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                   {{ old('is_active', isset($bankAccount) ? $bankAccount->is_active : true) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Active Bank Account</span>
                        </label>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Save Bank Account
                        </button>

                        <a href="{{ route('employees.bank-account.show', $employee) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>