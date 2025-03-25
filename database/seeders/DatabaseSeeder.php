<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        //$this->call(CreateFirstSuperAdminSeeder::class);


        /*

        // check
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Create or find super admin user
        $user = User::firstOrCreate(
            [
                'email' => 'super@admin.com'
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // if not, assign
        if (!$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        // Create a normal admin user
        /*$user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password321'),
        ]);
        $user-> assignRole('admin');
        */

        
    }
}
