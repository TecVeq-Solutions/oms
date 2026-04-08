<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\EmployeeBankAccount;
use App\Models\EmployeeSalaryPayment;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'employee_code',
        'full_name',
        'email',
        'phone',
        'department',
        'designation',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function shift()
    {
        return $this->belongsTo(\App\Models\Shift::class);
    }
    public function personalDetail()
    {
        return $this->hasOne(EmployeePersonalDetail::class);
    }
    public function bankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }

    public function activeBankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class)->where('is_active', true);
    }

    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class);
    }
}