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
        $this->call(CreateFirstSuperAdminSeeder::class);

        $this->call(TeacherSeeder::class);
    }
}
