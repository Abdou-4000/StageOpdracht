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
            'manage_permissions'
        ];

        // Create permissions if they don't exist(findOrCreate)
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // role creation
        $adminRole = Role::findOrCreate('admin', 'web');
        $superAdminRole = Role::findOrCreate('super_admin', 'web');

        // Assign specific permissions to admin
        $adminRole->syncPermissions([
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers'
        ]);

        // User creation + atleast a single admin
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
        
        //role assignment
        $superAdmin->syncRoles([$superAdminRole]);
        $admin->syncRoles([$adminRole]);
    }
}