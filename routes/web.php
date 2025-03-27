<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/map', [TeacherController::class, 'showMap'])->name('map');

Route::resource('teachers', TeacherController::class);
Route::resource('categories', CategoryController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
