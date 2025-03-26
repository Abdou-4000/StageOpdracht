<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RoleUserController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;




Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('login', function () {
    return Inertia::render('Auth/Login');
})->middleware(['auth', 'verified'])->name('login');

Route::get('register', function () {
    return Inertia::render('Auth/Register');
})->middleware(['role:super_admin'])->name('register');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('teachers', TeacherController::class);

Route::get('/debug-role', function () {
    $user = auth()->user()->load('roles', 'permissions');
    dd([
        'user' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getPermissionNames(),
        'is_super_admin' => $user->hasRole('super_admin'),
        'is_admin' => $user->hasRole('admin'),
        'role_name' => $user->getRoleNames(),
        'user_id' => $user->id,
        'auth_status' => auth()->check(),
        'can_manage_teachers' => $user->hasPermissionTo('view_teachers'),
        'can_manage_roles' => $user->hasPermissionTo('manage_roles'),
    ]);
})->middleware(['auth']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
