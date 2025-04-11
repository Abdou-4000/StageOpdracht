<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReviewController;

Route::get('/availabilities', [AvailabilityController::class, 'index']);

Route::post('/availabilities', [AvailabilityController::class, 'storeEvents']);

Route::get('/exceptions', [ExceptionController::class, 'index']);

Route::post('/exceptions', [ExceptionController::class, 'storeExceptions']);

Route::put('/exceptions/{id}', [ExceptionController::class, 'update']);

Route::delete('/exceptions/{id}', [ExceptionController::class, 'destroy']);

Route::get('/map/teachers', [MapController::class, 'index']);

Route::post('/reviews', [ReviewController::class, 'saveReview']);