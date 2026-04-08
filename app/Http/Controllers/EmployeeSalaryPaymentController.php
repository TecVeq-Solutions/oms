<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSalaryPaymentController extends Controller
{
    public function index(Employee $employee)
    {
        $payments = $employee->salaryPayments()
            ->with(['bankAccount', 'addedBy'])
            ->latest('year')
            ->latest('month')
            ->paginate(15);

        return view('employees.salary-payments.index', compact('employee', 'payments'));
    }

    public function create(Employee $employee)
    {
        $bankAccounts = $employee->bankAccounts()->where('is_active', true)->get();

        return view('employees.salary-payments.create', compact('employee', 'bankAccounts'));
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'bank_account_id' => ['nullable', 'exists:employee_bank_accounts,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee->salaryPayments()->create([
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'added_by' => Auth::id(),
        ]);

        return redirect()
            ->route('employees.salary-payments.index', $employee)
            ->with('success', 'Salary payment added successfully.');
    }

    public function myPayments()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $payments = $employee->salaryPayments()
            ->with(['bankAccount', 'addedBy'])
            ->latest('year')
            ->latest('month')
            ->paginate(15);

        return view('salary-payments.my', compact('employee', 'payments'));
    }
}