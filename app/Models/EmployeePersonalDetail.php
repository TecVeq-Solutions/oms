<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePersonalDetail extends Model
{
    protected $fillable = [
        'employee_id',

        // personal info
        'father_name',
        'cnic_number',
        'date_of_birth',
        'gender',

        // address
        'current_address',
        'permanent_address',
        'city',
        'country',

        // emergency
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',

        // documents
        'profile_photo',
        'cnic_front_photo',
        'cnic_back_photo',
        'document_1',
        'document_2',
        'document_3',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}