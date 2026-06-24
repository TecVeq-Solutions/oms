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
        'is_tracked',
        'tracking_api_token',
        'last_tracking_heartbeat',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'last_tracking_heartbeat' => 'datetime',
        'is_tracked' => 'boolean',
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

    public function screenshots()
    {
        return $this->hasMany(Screenshot::class);
    }

    public function todayScreenshots()
    {
        return $this->hasMany(Screenshot::class)->whereDate('captured_at', now()->toDateString());
    }

    public function latestScreenshot()
    {
        return $this->hasOne(Screenshot::class)->latestOfMany('captured_at');
    }

    public function getIsOnlineAttribute()
    {
        if (!$this->last_tracking_heartbeat) {
            return false;
        }
        return \Carbon\Carbon::parse($this->last_tracking_heartbeat)->diffInMinutes(now()) <= 10;
    }

    public function scopeTracked($query)
    {
        return $query->where('is_tracked', true);
    }
}