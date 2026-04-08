<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Employees</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage all employees from here.</p>
                    </div>

                    <a href="{{ route('employees.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Add Employee
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('employees.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search name, code, email..."
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <select name="status"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <select name="department"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Filter
                            </button>

                            <a href="{{ route('employees.index') }}"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Designation</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($employees as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $employee->employee_code }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $employee->full_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $employee->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $employee->department ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $employee->designation ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($employee->status === 'active')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a href="{{ route('employees.show', $employee) }}"
                                                class="px-3 py-1.5 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                                                View
                                            </a>

                                            <a href="{{ route('employees.edit', $employee) }}"
                                                class="px-3 py-1.5 rounded-md bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                Edit
                                            </a>

                                            <a href="{{ route('employees.bank-account.show', $employee) }}"
                                                class="px-3 py-1.5 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                Bank Account
                                            </a>

                                            <a href="{{ route('employees.salary-payments.index', $employee) }}"
                                                class="px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200">
                                                Salary Payments
                                            </a>

                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                        No employees found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>