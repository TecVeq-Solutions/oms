<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeBankAccountController extends Controller
{
    public function show(Employee $employee)
    {
        // dd('here');
        $bankAccount = $employee->bankAccounts()->latest()->first();

        return view('employees.bank-accounts.show', compact('employee', 'bankAccount'));
    }

    public function edit(Employee $employee)
    {
        $bankAccount = $employee->bankAccounts()->latest()->first();

        return view('employees.bank-accounts.edit', compact('employee', 'bankAccount'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'account_title' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'branch_code' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (($validated['is_active'] ?? true) == true) {
            EmployeeBankAccount::where('employee_id', $employee->id)->update(['is_active' => false]);
        }

        $bankAccount = $employee->bankAccounts()->latest()->first();

        if ($bankAccount) {
            $bankAccount->update([
                'bank_name' => $validated['bank_name'],
                'account_title' => $validated['account_title'],
                'account_number' => $validated['account_number'],
                'iban' => $validated['iban'] ?? null,
                'branch_name' => $validated['branch_name'] ?? null,
                'branch_code' => $validated['branch_code'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        } else {
            $employee->bankAccounts()->create([
                'bank_name' => $validated['bank_name'],
                'account_title' => $validated['account_title'],
                'account_number' => $validated['account_number'],
                'iban' => $validated['iban'] ?? null,
                'branch_name' => $validated['branch_name'] ?? null,
                'branch_code' => $validated['branch_code'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        }

        return redirect()
            ->route('employees.bank-account.show', $employee)
            ->with('success', 'Bank account saved successfully.');
    }

    public function myBankAccount()
    {
        abort_unless(auth()->user()->can('view own bank account'), 403);
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $bankAccount = $employee->activeBankAccount()->first();

        return view('bank-accounts.my', compact('employee', 'bankAccount'));
    }
}