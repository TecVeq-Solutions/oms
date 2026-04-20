<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'priority',
        'status',
        'estimated_minutes',
        'start_date',
        'due_date',
        'completed_at',
        'created_by',
        'is_active',
        'approved_extra_minutes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }
    public function timeLogs()
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    public function activeTimeLogs()
    {
        return $this->hasMany(TaskTimeLog::class)->where('is_running', true);
    }

    public function getConsumedMinutesAttribute(): int
    {
        return (int) $this->timeLogs()->sum('duration_minutes');
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->activeTimeLogs()->exists();
    }
    public function extensionRequests()
    {
        return $this->hasMany(TaskExtensionRequest::class);
    }

    public function getAllowedMinutesAttribute(): int
    {
        return (int) $this->estimated_minutes + (int) $this->approved_extra_minutes;
    }

    public function getRemainingAllowedMinutesAttribute(): int
    {
        return max(0, $this->allowed_minutes - $this->consumed_minutes);
    }


    public function getUnapprovedOverrunMinutesAttribute(): int
    {
        return max(0, $this->consumed_minutes - $this->allowed_minutes);
    }

    public function getIsOvertimeExceededAttribute(): bool
    {
        return $this->unapproved_overrun_minutes > 0;
    }

    public function getUsageProgressPercentageAttribute(): float
    {
        if ((int) $this->allowed_minutes <= 0) {
            return 0;
        }

        return min(100, round(($this->consumed_minutes / $this->allowed_minutes) * 100, 2));
    }
    public function comments()
{
    return $this->hasMany(TaskComment::class)->latest();
}
public function attachments()
{
    return $this->hasMany(TaskAttachment::class)->latest();
}
public function activityLogs()
{
    return $this->hasMany(TaskActivityLog::class)->latest();
}











public function getIsOverdueAttribute(): bool
{
    return $this->due_date
        && now()->startOfDay()->gt($this->due_date->copy()->startOfDay())
        && $this->status !== 'completed';
}
}