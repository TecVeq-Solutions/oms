<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'overtime_minutes',
        'break_minutes',
        'worked_minutes',
        'ip_address',
        'device_name',
        'browser',
        'platform',
        'user_agent',
        'latitude',
        'longitude',
        'distance_from_office',
        'photo_path',
        'privacy_note',
        'is_suspicious',
        'suspicious_reason',
        'checkout_latitude',
        'checkout_longitude',
        'checkout_distance_from_office',
        'checkout_photo_path',
        'checkout_privacy_note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_suspicious' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function shift()
    {
        return $this->belongsTo(\App\Models\Shift::class);
    }
}