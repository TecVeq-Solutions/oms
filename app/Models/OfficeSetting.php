<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name',
        'office_email',
        'office_phone',
        'office_address',
        'office_latitude',
        'office_longitude',
        'allowed_radius_meters',
        'office_start_time',
        'late_after',
        'selfie_required',
        'location_required',
        'device_tracking_enabled',
        'screenshot_interval_minutes',
        'screenshot_compression_quality',
        'office_end_time',
    ];

    public function getTrackingConfig()
    {
        return [
            'office_start' => $this->office_start_time,
            'office_end' => $this->office_end_time,
            'interval_seconds' => ($this->screenshot_interval_minutes ?? 10) * 60,
            'compression_quality' => $this->screenshot_compression_quality ?? 60,
        ];
    }
}