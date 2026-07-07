<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WolfinRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'manage wolfin otps', 'guard_name' => 'web']);
        
        $role = Role::firstOrCreate(['name' => 'wolfin support', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo($permission);
    }
}
