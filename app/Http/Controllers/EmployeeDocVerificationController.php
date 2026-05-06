<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\AppNotificationService;
use Illuminate\Http\Request;

class EmployeeDocVerificationController extends Controller
{
    public function __construct(
        protected AppNotificationService $notificationService
    ) {}

    /**
     * List of valid document fields with human-readable labels.
     */
    private function docFields(): array
    {
        return [
            'cnic_front_photo' => ['status' => 'cnic_front_status', 'reject' => 'cnic_front_reject_reason', 'label' => 'CNIC Front'],
            'cnic_back_photo'  => ['status' => 'cnic_back_status',  'reject' => 'cnic_back_reject_reason',  'label' => 'CNIC Back'],
            'document_1'       => ['status' => 'document_1_status', 'reject' => 'document_1_reject_reason', 'label' => 'Document 1'],
            'document_2'       => ['status' => 'document_2_status', 'reject' => 'document_2_reject_reason', 'label' => 'Document 2'],
            'document_3'       => ['status' => 'document_3_status', 'reject' => 'document_3_reject_reason', 'label' => 'Document 3'],
        ];
    }

    public function verify(Employee $employee, string $field)
    {
        abort_unless(auth()->user()->can('edit employees'), 403);

        $fields = $this->docFields();
        abort_unless(array_key_exists($field, $fields), 404);

        $personal = $employee->personalDetail;
        abort_unless($personal && $personal->$field, 404);

        $meta = $fields[$field];
        $personal->update([
            $meta['status'] => 'verified',
            $meta['reject'] => null,
        ]);

        // Notify employee
        if ($employee->user_id) {
            $this->notificationService->create(
                $employee->user_id,
                'document_verified',
                'Document Verified',
                "Your {$meta['label']} has been verified by admin.",
                route('profile.employee'),
                ['employee_id' => $employee->id, 'field' => $field]
            );
        }

        return back()->with('success', "{$meta['label']} verified successfully.");
    }

    public function reject(Employee $employee, string $field, Request $request)
    {
        abort_unless(auth()->user()->can('edit employees'), 403);

        $fields = $this->docFields();
        abort_unless(array_key_exists($field, $fields), 404);

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $personal = $employee->personalDetail;
        abort_unless($personal && $personal->$field, 404);

        $meta = $fields[$field];
        $personal->update([
            $meta['status'] => 'rejected',
            $meta['reject'] => $request->reject_reason,
        ]);

        // Notify employee
        if ($employee->user_id) {
            $this->notificationService->create(
                $employee->user_id,
                'document_rejected',
                'Document Rejected',
                "Your {$meta['label']} was rejected. Reason: {$request->reject_reason}",
                route('profile.employee'),
                ['employee_id' => $employee->id, 'field' => $field]
            );
        }

        return back()->with('success', "{$meta['label']} rejected.");
    }
}
