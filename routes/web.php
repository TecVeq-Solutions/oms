<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OfficeSettingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadNoteController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\AIGenerationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\LeadReportController;
use App\Http\Controllers\EmailCampaignReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AllowedIpController;
use App\Http\Controllers\EmployeeBankAccountController;
use App\Http\Controllers\EmployeeSalaryPaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Bank Accounts & Salary Payments (Admin / HR)
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view employees')->group(function () {
        Route::get('/employees/{employee}/bank-account', [EmployeeBankAccountController::class, 'show'])
            ->name('employees.bank-account.show');
    });

    Route::middleware('permission:edit employees')->group(function () {
        Route::get('/employees/{employee}/bank-account/edit', [EmployeeBankAccountController::class, 'edit'])
            ->name('employees.bank-account.edit');

        Route::put('/employees/{employee}/bank-account', [EmployeeBankAccountController::class, 'update'])
            ->name('employees.bank-account.update');
    });

    Route::middleware('permission:view salary payments')->group(function () {
        Route::get('/employees/{employee}/salary-payments', [EmployeeSalaryPaymentController::class, 'index'])
            ->name('employees.salary-payments.index');
    });

    Route::middleware('permission:create salary payments')->group(function () {
        Route::get('/employees/{employee}/salary-payments/create', [EmployeeSalaryPaymentController::class, 'create'])
            ->name('employees.salary-payments.create');

        Route::post('/employees/{employee}/salary-payments', [EmployeeSalaryPaymentController::class, 'store'])
            ->name('employees.salary-payments.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Employee Self Service
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/my-profile', [ProfileController::class, 'employeeProfile'])->name('profile.employee');

        Route::middleware(['feature:attendance_module_enabled'])->group(function () {
            Route::get('/my-attendance', [ProfileController::class, 'myAttendance'])->name('profile.attendance');

            Route::middleware('permission:mark self attendance')->group(function () {
                Route::get('/my-attendance/check-in', [ProfileController::class, 'checkInForm'])->name('profile.checkin.form');
                Route::post('/my-attendance/check-in', [ProfileController::class, 'storeCheckIn'])->name('profile.checkin.store');
            });

            Route::middleware('permission:mark self checkout')->group(function () {
                Route::get('/my-attendance/check-out', [ProfileController::class, 'checkOutForm'])->name('profile.checkout.form');
                Route::post('/my-attendance/check-out', [ProfileController::class, 'storeCheckOut'])->name('profile.checkout.store');
            });
        });

        Route::middleware('permission:view own bank account')->group(function () {
            Route::get('/my-bank-account', [EmployeeBankAccountController::class, 'myBankAccount'])
                ->name('bank-account.my');
        });

        Route::middleware('permission:view own salary payments')->group(function () {
            Route::get('/my-salary-payments', [EmployeeSalaryPaymentController::class, 'myPayments'])
                ->name('salary-payments.my');
        });

        Route::middleware(['permission:view own leads', 'feature:lead_module_enabled'])->group(function () {
            Route::get('/my-leads', [LeadController::class, 'myLeads'])->name('leads.my');
        });

        Route::middleware(['feature:leave_module_enabled'])->group(function () {
            Route::middleware('permission:apply leave')->group(function () {
                Route::get('/my-leaves', [LeaveRequestController::class, 'myLeaves'])->name('leave-requests.my');
                Route::get('/my-leaves/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
                Route::post('/my-leaves', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
            });

            Route::middleware('permission:cancel own leave')->group(function () {
                Route::patch('/my-leaves/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
            });

            Route::middleware('permission:view own leave balance')->group(function () {
                Route::get('/my-leave-balance', [LeaveRequestController::class, 'balance'])->name('leave-requests.balance');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view employees')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    });

    Route::middleware('permission:create employees')->group(function () {
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    });

    Route::middleware('permission:edit employees')->group(function () {
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    });

    Route::middleware('permission:delete employees')->group(function () {
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */
    Route::middleware(['feature:attendance_module_enabled'])->group(function () {
        Route::middleware('permission:view attendance')->group(function () {
            Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
            Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
        });

        Route::middleware('permission:create attendance')->group(function () {
            Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
            Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        });

        Route::middleware('permission:edit attendance')->group(function () {
            Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
            Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        });

        Route::middleware('permission:delete attendance')->group(function () {
            Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Office Settings
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view office settings')->group(function () {
        Route::get('/office-settings', [OfficeSettingController::class, 'edit'])->name('office-settings.edit');
    });

    Route::middleware('permission:edit office settings')->group(function () {
        Route::put('/office-settings', [OfficeSettingController::class, 'update'])->name('office-settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Leads
    |--------------------------------------------------------------------------
    */
    Route::middleware(['feature:lead_module_enabled'])->group(function () {
        Route::middleware('permission:create leads')->group(function () {
            Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
            Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        });

        Route::middleware('permission:view leads')->group(function () {
            Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        });

        Route::middleware('permission:edit leads')->group(function () {
            Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
            Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        });

        Route::middleware('permission:delete leads')->group(function () {
            Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
        });

        Route::middleware('permission:add lead notes')->group(function () {
            Route::post('/leads/{lead}/notes', [LeadNoteController::class, 'store'])->name('leads.notes.store');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Email Campaigns
    |--------------------------------------------------------------------------
    */
    Route::middleware(['feature:campaign_module_enabled'])->group(function () {
        Route::middleware('permission:create campaigns')->group(function () {
            Route::get('/email-campaigns/create', [EmailCampaignController::class, 'create'])->name('email-campaigns.create');
            Route::post('/email-campaigns', [EmailCampaignController::class, 'store'])->name('email-campaigns.store');
        });

        Route::middleware('permission:view campaigns')->group(function () {
            Route::get('/email-campaigns', [EmailCampaignController::class, 'index'])->name('email-campaigns.index');
            Route::get('/email-campaigns/{email_campaign}', [EmailCampaignController::class, 'show'])->name('email-campaigns.show');
        });

        Route::middleware('permission:edit campaigns')->group(function () {
            Route::get('/email-campaigns/{email_campaign}/edit', [EmailCampaignController::class, 'edit'])->name('email-campaigns.edit');
            Route::put('/email-campaigns/{email_campaign}', [EmailCampaignController::class, 'update'])->name('email-campaigns.update');
        });

        Route::middleware('permission:delete campaigns')->group(function () {
            Route::delete('/email-campaigns/{email_campaign}', [EmailCampaignController::class, 'destroy'])->name('email-campaigns.destroy');
        });

        Route::middleware('permission:send campaigns')->group(function () {
            Route::post('/email-campaigns/{email_campaign}/send', [EmailCampaignController::class, 'send'])->name('email-campaigns.send');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AI
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:use ai tools', 'feature:ai_module_enabled', 'throttle:10,1'])->group(function () {
        Route::post('/ai/generate-campaign-content', [AIController::class, 'generateCampaignContent'])
            ->name('ai.generate-campaign-content');

        Route::get('/ai/leads/{lead}/insights', [AIController::class, 'leadInsights'])
            ->name('ai.leads.insights');
    });

    Route::middleware(['permission:view ai history', 'feature:ai_module_enabled'])->group(function () {
        Route::get('/ai-generations', [AIGenerationController::class, 'index'])->name('ai-generations.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:view attendance reports', 'feature:attendance_module_enabled'])->group(function () {
        Route::get('/reports/attendance', [AttendanceReportController::class, 'index'])->name('reports.attendance');
    });

    Route::middleware(['permission:view lead reports', 'feature:lead_module_enabled'])->group(function () {
        Route::get('/reports/leads', [LeadReportController::class, 'index'])->name('reports.leads');
    });

    Route::middleware(['permission:view campaign reports', 'feature:campaign_module_enabled'])->group(function () {
        Route::get('/reports/email-campaigns', [EmailCampaignReportController::class, 'index'])->name('reports.email-campaigns');
    });

    Route::middleware(['permission:view leave reports', 'feature:leave_module_enabled'])->group(function () {
        Route::get('/reports/leaves', [LeaveReportController::class, 'index'])->name('reports.leaves');
    });

    Route::middleware('permission:export reports')->group(function () {
        Route::get('/reports/attendance/export', [ReportExportController::class, 'attendance'])
            ->middleware('feature:attendance_module_enabled')
            ->name('reports.attendance.export');

        Route::get('/reports/leads/export', [ReportExportController::class, 'leads'])
            ->middleware('feature:lead_module_enabled')
            ->name('reports.leads.export');

        Route::get('/reports/email-campaigns/export', [ReportExportController::class, 'campaigns'])
            ->middleware('feature:campaign_module_enabled')
            ->name('reports.email-campaigns.export');

        Route::get('/reports/leaves/export', [LeaveReportController::class, 'export'])
            ->middleware('feature:leave_module_enabled')
            ->name('reports.leaves.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Leave Types & Requests
    |--------------------------------------------------------------------------
    */
    Route::middleware(['feature:leave_module_enabled'])->group(function () {
        Route::middleware('permission:view leave types')->group(function () {
            Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
        });

        Route::middleware('permission:create leave types')->group(function () {
            Route::get('/leave-types/create', [LeaveTypeController::class, 'create'])->name('leave-types.create');
            Route::post('/leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
        });

        Route::middleware('permission:edit leave types')->group(function () {
            Route::get('/leave-types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('leave-types.edit');
            Route::put('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
        });

        Route::middleware('permission:delete leave types')->group(function () {
            Route::delete('/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');
        });

        Route::middleware('permission:view leave requests')->group(function () {
            Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
        });

        Route::middleware('permission:approve leave')->group(function () {
            Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        });

        Route::middleware('permission:reject leave')->group(function () {
            Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
        Route::get('/user-roles/{user}/edit', [UserRoleController::class, 'edit'])->name('user-roles.edit');
        Route::put('/user-roles/{user}', [UserRoleController::class, 'update'])->name('user-roles.update');
        Route::resource('allowed-ips', AllowedIpController::class)->except(['show']);
        Route::resource('shifts', ShiftController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view notifications')->group(function () {
        Route::get('/notifications', [AppNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [AppNotificationController::class, 'show'])->name('notifications.show');
        Route::patch('/notifications/{notification}/read', [AppNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::patch('/notifications/{notification}/quick-read', [AppNotificationController::class, 'quickRead'])->name('notifications.quick-read');
        Route::patch('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

require __DIR__.'/auth.php';