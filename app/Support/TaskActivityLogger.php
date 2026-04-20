<?php

namespace App\Support;

use App\Models\Task;
use App\Models\TaskActivityLog;

class TaskActivityLogger
{
    public static function log(?Task $task, ?int $userId, string $action, ?string $description = null, array $meta = []): void
    {
        TaskActivityLog::create([
            'task_id' => $task?->id,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'meta' => $meta,
        ]);
    }
}