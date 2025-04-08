<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ExceptionController;

Route::get('/availabilities', [AvailabilityController::class, 'index']);

Route::post('/availabilities', [AvailabilityController::class, 'storeEvents']);

Route::get('/exceptions', [ExceptionController::class, 'index']);

Route::post('/exceptions', [ExceptionController::class, 'storeExceptions']);