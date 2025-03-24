<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/map-test', function () {
    return view('map');
});

Route::resource('teachers', TeacherController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
