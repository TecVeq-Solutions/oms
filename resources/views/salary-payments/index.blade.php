<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Salary Payments</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $employee->full_name ?? $employee->name ?? 'Employee' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('employees.salary-payments.create', $employee) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Add Salary Payment
                        </a>

                        <a href="{{ route('employees.bank-account.show', $employee) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            View Bank Account
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 rounded-xl px-4 py-3 shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-xl p-6">
                @if($payments->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Year</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Added By</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ date('F', mktime(0, 0, 0, $payment->month, 1)) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->year }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->payment_date?->format('Y-m-d') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->transaction_reference ?: '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->addedBy->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        No salary payments have been added yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>