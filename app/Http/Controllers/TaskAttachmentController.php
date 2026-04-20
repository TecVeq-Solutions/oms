<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\TaskActivityLogger;
use App\Services\AppNotificationService;

class TaskAttachmentController extends Controller
{
    public function __construct(
        protected AppNotificationService $appNotificationService
    ) {
    }

    public function store(Request $request, Task $task)
    {
        abort_unless(auth()->user()->can('upload task attachments'), 403);

        $user = auth()->user();

        $canAccessTask = $user->can('view tasks')
            || $task->assignedUsers()->where('users.id', $user->id)->exists();

        if (!$canAccessTask) {
            return back()->with('error', 'You are not allowed to upload attachments for this task.');
        }

        $request->validate([
            'attachments' => ['required', 'array', 'min:1'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,csv,zip,rar,txt',
            ],
        ]);

        $uploadedFiles = [];

        foreach ($request->file('attachments', []) as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $storedName = Str::uuid() . ($extension ? '.' . $extension : '');

            $filePath = $file->storeAs('task_attachments/' . $task->id, $storedName, 'public');

            $attachment = TaskAttachment::create([
                'task_id' => $task->id,
                'uploaded_by' => $user->id,
                'file_name' => $storedName,
                'original_name' => $originalName,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $uploadedFiles[] = [
                'id' => $attachment->id,
                'original_name' => $originalName,
                'file_size' => $file->getSize(),
            ];

            TaskActivityLogger::log(
                $task,
                $user->id,
                'attachment_uploaded',
                'Task attachment uploaded.',
                [
                    'attachment_id' => $attachment->id,
                    'file_name' => $originalName,
                    'file_size' => $file->getSize(),
                ]
            );
        }

        $notifyUserIds = $task->assignedUsers()
            ->where('users.id', '!=', $user->id)
            ->pluck('users.id')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($notifyUserIds)) {
            $fileCount = count($uploadedFiles);

            $this->appNotificationService->notifyUsers(
                $notifyUserIds,
                'task_attachment_uploaded',
                'Task Attachment Uploaded',
                $user->name . ' uploaded ' . $fileCount . ' attachment(s) to task: ' . $task->title,
                route('tasks.show', $task),
                [
                    'task_id' => $task->id,
                    'uploaded_by' => $user->id,
                    'attachment_ids' => collect($uploadedFiles)->pluck('id')->values()->toArray(),
                    'files_count' => $fileCount,
                ]
            );
        }

        return back()->with('success', 'Attachment(s) uploaded successfully.');
    }
}