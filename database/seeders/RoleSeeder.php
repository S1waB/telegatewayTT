<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-device-types',
            'manage-devices',
            'assign-devices',
            'send-commands',
            'view-history',
            'view-dashboard'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator with full access']);
        $adminRole->givePermissionTo(Permission::all());

        $operatorRole = Role::create(['name' => 'operator', 'description' => 'Operator with restricted access']);
        $operatorRole->givePermissionTo([
            'view-dashboard',
            'send-commands',
            'view-history'
        ]);
    }
}
