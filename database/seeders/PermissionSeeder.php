<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // dashboard
            'view dashboard',

            // employees
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',

            // attendance
            'view attendance',
            'create attendance',
            'edit attendance',
            'delete attendance',
            'mark self attendance',
            'mark self checkout',

            // leave types
            'view leave types',
            'create leave types',
            'edit leave types',
            'delete leave types',

            // leave requests
            'view leave requests',
            'apply leave',
            'cancel own leave',
            'approve leave',
            'reject leave',
            'view own leave balance',

            // leads
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'view own leads',
            'add lead notes',

            // email campaigns
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'delete campaigns',
            'send campaigns',

            // reports
            'view attendance reports',
            'view lead reports',
            'view campaign reports',
            'view leave reports',
            'export reports',

            // office settings
            'view office settings',
            'edit office settings',

            // notifications
            'view notifications',

            // ai
            'use ai tools',
            'view ai history',

            // leave reports / system pages
            'view leave types module',
            'view notifications module',
            'view workspaces',
            'create workspace',
            'edit workspace',
            'delete workspace',

            'view projects',
            'create project',
            'edit project',
            'delete project',

            'view tasks',
            'create task',
            'edit task',
            'delete task',
            'assign task',
            'view own tasks',
            'start own task',
            'stop own task',
            'complete own task',
            'request task extension',
            'approve task extension',
            'comment on tasks',
            'upload task attachments',
            'view task reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $hr = Role::firstOrCreate(['name' => 'hr']);
        $sales = Role::firstOrCreate(['name' => 'sales']);
        $manager = Role::firstOrCreate(['name' => 'manager']);

        // Admin gets everything
        $admin->syncPermissions(Permission::pluck('name')->toArray());

        // Employee permissions
        $employee->syncPermissions([
            'view dashboard',
            'mark self attendance',
            'mark self checkout',
            'apply leave',
            'cancel own leave',
            'view own leave balance',
            'view own leads',
            'view notifications',
        ]);

        // HR permissions
        $hr->syncPermissions([
            'view dashboard',

            'view employees',
            'create employees',
            'edit employees',

            'view attendance',
            'edit attendance',

            'view leave types',
            'create leave types',
            'edit leave types',

            'view leave requests',
            'approve leave',
            'reject leave',

            'view attendance reports',
            'view leave reports',
            'export reports',

            'view notifications',
        ]);

        // Sales permissions
        $sales->syncPermissions([
            'view dashboard',

            'view leads',
            'create leads',
            'edit leads',
            'add lead notes',
            'view own leads',

            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'send campaigns',

            'view lead reports',
            'view campaign reports',
            'export reports',

            'use ai tools',
            'view notifications',
        ]);

        // Manager permissions
        $manager->syncPermissions([
            'view dashboard',

            'view employees',
            'view attendance',
            'view leave requests',
            'approve leave',
            'reject leave',

            'view leads',
            'add lead notes',

            'view attendance reports',
            'view lead reports',
            'view leave reports',

            'view notifications',
        ]);
    }
}