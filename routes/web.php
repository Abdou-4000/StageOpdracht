<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
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
    return redirect()->route('map');
})->name('home');

Route::get('login', function () {
    return Inertia::render('Auth/Login');
})->middleware(['auth', 'verified'])->name('login');

Route::get('register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('dashboard');    
        Route::put('/users/{user}/roles', [SuperAdminController::class, 'syncRoles'])->name('users.roles.sync');
        Route::put('/users/{user}/permissions', [SuperAdminController::class, 'syncPermissions'])->name('users.permissions.sync');
        Route::post('/users', [SuperAdminController::class, 'createUser'])->name('users.create');
        Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('users.delete');
        Route::put('/users/{user}/update-field', [SuperAdminController::class, 'updateField'])->name('users.update.field');
        Route::put('/users/{user}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('users.reset.password');
        Route::put('/users/{user}/access', [SuperAdminController::class, 'updateAccess'])->name('users.access.update');
    });
});


Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat')->middleware(['auth', 'role:superadmin']);

// CRUD routes (Teacher/Categories)
Route::resource('teachers', TeacherController::class)->middleware(['auth', 'role:admin']);
Route::resource('categories', CategoryController::class)->middleware(['auth', 'role:admin']);

// CSV import route
Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import')->middleware(['auth', 'role:admin']);

// Export routes (Excel/PDF) 
Route::get('/export-full-excel', [ExportController::class, 'exportExcel'])->middleware(['auth', 'role:admin']);
Route::get('/export-pdf', [ExportController::class, 'exportPDF'])->middleware(['auth', 'role:admin']);

// Made by
Route::get('/madeby', function () {
    return Inertia::render('MadeBy');
})->name('madeby');

// Agenda
Route::get('/agenda/{teacher}', [AgendaController::class, 'index'])->name('agenda')->middleware(['auth']);

// Teacher Profiles
Route::get('/teacherprofile', [ProfileController::class, 'index'])->name('teacherprofile')->middleware(['auth', 'role:teacher']);

// Map
Route::get('/map', [MapController::class, 'map'])->name('map');

Route::get('/debug-role', function () {
    $user = auth()->user()->load('roles', 'permissions');
    dd([
        'user' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getPermissionNames(),
        'is_super_admin' => $user->hasRole('super_admin'),
        'is_admin' => $user->hasRole('admin'),
        'user_id' => $user->id,
        'auth_status' => auth()->check(),
        'can_manage_teachers' => $user->hasPermissionTo('view_teachers'),
        'can_manage_roles' => $user->hasPermissionTo('manage_roles'),
    ]);
})->middleware(['auth']);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
