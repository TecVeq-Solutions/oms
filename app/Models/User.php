<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }
    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class)->latest();
    }
    public function unreadAppNotifications()
    {
        return $this->hasMany(AppNotification::class)->whereNull('read_at')->latest();
    }
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function isHR(): bool
    {
        return $this->hasRole('hr');
    }

    public function isSales(): bool
    {
        return $this->hasRole('sales');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }
    public function allowedIps()
    {
        return $this->hasMany(\App\Models\AllowedIp::class);
    }
    public function createdWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'created_by');
    }

    public function workspaceMemberships()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members')
            ->withPivot('role_in_workspace')
            ->withTimestamps();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }
    public function taskTimeLogs()
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    public function runningTaskTimeLog()
    {
        return $this->hasOne(TaskTimeLog::class)->where('is_running', true);
    }
    public function taskExtensionRequests()
    {
        return $this->hasMany(TaskExtensionRequest::class);
    }

    public function reviewedTaskExtensionRequests()
    {
        return $this->hasMany(TaskExtensionRequest::class, 'reviewed_by');
    }
    public function taskComments()
{
    return $this->hasMany(TaskComment::class);
}

public function mentionedInTaskComments()
{
    return $this->belongsToMany(TaskComment::class, 'task_comment_mentions', 'mentioned_user_id', 'task_comment_id')
        ->withTimestamps();
}
public function taskAttachments()
{
    return $this->hasMany(TaskAttachment::class, 'uploaded_by');
}
public function taskActivityLogs()
{
    return $this->hasMany(TaskActivityLog::class);
}
}