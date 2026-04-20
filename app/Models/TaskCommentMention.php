<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskCommentMention extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_comment_id',
        'mentioned_user_id',
    ];

    public function taskComment()
    {
        return $this->belongsTo(TaskComment::class);
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }
}