<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBankAccount extends Model
{
    protected $fillable = [
        'employee_id',
        'bank_name',
        'account_title',
        'account_number',
        'iban',
        'branch_name',
        'branch_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class, 'bank_account_id');
    }
}