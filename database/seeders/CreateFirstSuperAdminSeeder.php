<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CreateFirstSuperAdminSeeder extends Seeder
{


    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Defined permissions
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
            'manage_permissions',
            'edit_categories',
            'edit_profile',
            'view_profile'
        ];

        // Create permissions if they don't exist(findOrCreate)
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // role creation
        $adminRole = Role::findOrCreate('admin', 'web');
        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $userRole = Role::findOrCreate('user', 'web');

        // Assign specific permissions to admin and user roles
        $adminRole->syncPermissions([
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers'
        ]);

        $userRole->syncPermissions([
            'view_teachers',
            'view_profile',
            'edit_profile'
        ]);

        // User creation + atleast a single admin, super admin and user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123')
            ]
        );
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123')
            ]
        );    
        
        $user = User::firstOrcreate(
            ['email' =>  'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password123')
            ]

            );
        //role assignment
        $superAdmin->syncRoles([$superAdminRole]);
        $admin->syncRoles([$adminRole]);
        $user->syncRoles([$userRole]);
    }
}