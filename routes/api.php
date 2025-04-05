<?php

use App\Http\Controllers\AvailabilityController;

Route::get('/availabilities', [AvailabilityController::class, 'index']);

Route::post('/availabilities', [AvailabilityController::class, 'storeEvents']);