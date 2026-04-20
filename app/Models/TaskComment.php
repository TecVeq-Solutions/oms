<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentions()
    {
        return $this->hasMany(TaskCommentMention::class);
    }

    public function mentionedUsers()
    {
        return $this->belongsToMany(User::class, 'task_comment_mentions', 'task_comment_id', 'mentioned_user_id')
            ->withTimestamps();
    }
}