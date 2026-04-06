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
    ];
}