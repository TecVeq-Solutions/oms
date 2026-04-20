<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Services\AppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\TaskActivityLogger;

class TaskCommentController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function store(Request $request, Task $task)
    {
        abort_unless(auth()->user()->can('comment on tasks'), 403);

        $user = auth()->user();

        $canAccessTask = $user->can('view tasks')
            || $task->assignedUsers()->where('users.id', $user->id)->exists();

        if (!$canAccessTask) {
            return back()->with('error', 'You are not allowed to comment on this task.');
        }

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:5000'],
        ]);

        $comment = null;
        $mentionedUsers = collect();

        DB::transaction(function () use ($task, $user, $validated, &$comment, &$mentionedUsers) {
            $comment = TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'comment' => $validated['comment'],
            ]);

            $mentionedUsers = $this->extractMentionedUsers($validated['comment'], $task);

            if ($mentionedUsers->isNotEmpty()) {
                $comment->mentionedUsers()->syncWithoutDetaching(
                    $mentionedUsers->pluck('id')->toArray()
                );
            }

            TaskActivityLogger::log(
                $task,
                $user->id,
                'task_comment_added',
                'Task comment added.',
                [
                    'task_comment_id' => $comment->id,
                ]
            );
        });

        // 1) Notify mentioned users
        $mentionedUserIds = $mentionedUsers
            ->pluck('id')
            ->filter(fn ($id) => (int) $id !== (int) $user->id)
            ->unique()
            ->values()
            ->toArray();

        if (!empty($mentionedUserIds)) {
            $this->appNotificationService->notifyUsers(
                $mentionedUserIds,
                'task_comment_mention',
                'You were mentioned in a task comment',
                "{$user->name} mentioned you in task: {$task->title}",
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'task_comment_id' => $comment->id,
                    'mentioned_by' => $user->id,
                ]
            );
        }

        // 2) Notify other assigned users about new comment
        $notifyUserIds = $task->assignedUsers()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->filter(fn ($id) => !in_array((int) $id, $mentionedUserIds))
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_comment_added',
                'New Task Comment',
                "{$user->name} commented on task: {$task->title}",
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'task_comment_id' => $comment->id,
                    'commented_by' => $user->id,
                ]
            );
        }

        return back()->with('success', 'Comment added successfully.');
    }

    private function extractMentionedUsers(string $commentText, Task $task)
    {
        preg_match_all('/@([A-Za-z0-9._-]+)/', $commentText, $matches);

        $usernames = collect($matches[1] ?? [])
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        if ($usernames->isEmpty()) {
            return collect();
        }

        $assignedUserIds = $task->assignedUsers()->pluck('users.id');

        return User::query()
            ->whereIn('id', $assignedUserIds)
            ->where(function ($query) use ($usernames) {
                foreach ($usernames as $username) {
                    $query->orWhere('name', 'like', $username)
                          ->orWhere('email', 'like', $username . '%');
                }
            })
            ->get()
            ->unique('id')
            ->values();
    }
}