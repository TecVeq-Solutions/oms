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
    public function getActiveRoleAttribute()
    {
        return session('active_role') ?? $this->roles->first()?->name;
    }

    public function isAdmin(): bool
    {
        return $this->active_role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->active_role === 'employee';
    }

    public function isHR(): bool
    {
        return $this->active_role === 'hr';
    }

    public function isSales(): bool
    {
        return $this->active_role === 'sales';
    }

    public function isManager(): bool
    {
        return $this->active_role === 'manager';
    }

    public function hasRole($roles, $guard = null): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = explode('|', $roles);
        }

        if (is_string($roles)) {
            return $this->active_role === $roles;
        }

        if (is_array($roles) || $roles instanceof \Illuminate\Support\Collection) {
            $roles = collect($roles)->map(fn($r) => is_object($r) ? $r->name : $r)->toArray();
            return in_array($this->active_role, $roles);
        }

        return false;
    }

    public function hasAnyRole(...$roles): bool
    {
        if (isset($roles[0]) && is_array($roles[0])) {
            $roles = $roles[0];
        }

        return in_array($this->active_role, $roles);
    }

    public function hasAllRoles(...$roles): bool
    {
        if (isset($roles[0]) && is_array($roles[0])) {
            $roles = $roles[0];
        }

        return count($roles) === 1 && $roles[0] === $this->active_role;
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $activeRole = $this->active_role;
        if (!$activeRole) {
            return false;
        }

        try {
            $role = \Spatie\Permission\Models\Role::findByName($activeRole, $guardName ?? $this->getDefaultGuardName());
            return $role->hasPermissionTo($permission);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            return false;
        }
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