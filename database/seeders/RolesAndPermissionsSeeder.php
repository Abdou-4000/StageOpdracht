<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers',
            'view_admins',
            'create_admins',
            'edit_admins',
            'delete_admins',
            'manage_roles',
            'manage_permissions'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles if they don't exist
        $admin = Role::findOrCreate('admin', 'web');
        $superAdmin = Role::findOrCreate('super_admin', 'web');

        // Assign all permissions to super admin
        $superAdmin->syncPermissions(Permission::all());

        // Assign specific permissions to admin
        $admin->syncPermissions([
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers'
        ]);

        // Create super admin user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123')
            ]
        );
        
        // Assign super admin role
        $user->syncRoles([$superAdmin]);
    }
}