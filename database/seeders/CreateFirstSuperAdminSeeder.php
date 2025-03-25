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


        










        /*
        // Check if role exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();

        if (!$superAdminRole || !$adminRole) {
            Log::error('Roles not found. Please run RolesAndPermissionsSeeder first.');
            return;
        }
        */
        // Create Super Admin

                                                                  /*

        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123')
            ]
        );

        // Clear and reassign role
        //$superAdmin->syncRoles([]);
        $user->assignRole($role);

        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123')
            ]
        );
                                                                */
        // Clear and reassign role
        //$admin->syncRoles([]);
        //$admin->assignRole('admin');

        // Verify assignments
        //Log::info('Super Admin role assigned: ' . $superAdmin->hasRole('super_admin'));
        //Log::info('Admin role assigned: ' . $admin->hasRole('admin'));
    }






    /*
    public function run(): void
    {
        $superAdmin = User::firstOrCreate([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123')
        ]);
        $superAdmin->assignRole('super_admin');


        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123')
        ]);
        $admin->assignRole('admin');
    }   
    */
    
}