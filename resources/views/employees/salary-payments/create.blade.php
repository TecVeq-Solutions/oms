<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800">Add Salary Payment</h2>
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
                <form action="{{ route('employees.salary-payments.store', $employee) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Account</label>
                            <select name="bank_account_id"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Bank Account</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->bank_name }} - {{ $account->account_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <select name="month"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Select Month</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('month') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <input type="number"
                                   name="year"
                                   min="2000"
                                   max="2100"
                                   value="{{ old('year', now()->year) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                            <input type="date"
                                   name="payment_date"
                                   value="{{ old('payment_date') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Reference</label>
                            <input type="text"
                                   name="transaction_reference"
                                   value="{{ old('transaction_reference') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes"
                                      rows="4"
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>