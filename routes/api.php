<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReviewController;

// availability routes
Route::get('/availabilities/{id}', [AvailabilityController::class, 'index']);

Route::post('/availabilities/{id}', [AvailabilityController::class, 'storeEvents'])->middleware(['auth:sanctum', 'ownOrAdmin']);

// exception routes
Route::get('/exceptions/{id}', [ExceptionController::class, 'index']);

Route::post('/exceptions/{id}', [ExceptionController::class, 'storeExceptions'])->middleware(['auth:sanctum', 'ownOrAdmin']);

Route::put('/exceptions/{id}', [ExceptionController::class, 'update'])->middleware(['auth:sanctum', 'ownOrAdmin']);

Route::delete('/exceptions/{id}', [ExceptionController::class, 'destroy'])->middleware(['auth:sanctum', 'ownOrAdmin']);

// map route
Route::get('/map/teachers', [MapController::class, 'index']);

// Reviews routes
Route::post('/reviews', [ReviewController::class, 'saveReview'])->middleware(['auth:sanctum', 'role:user']);
Route::get('/teachers/{teacher}/reviews', [ReviewController::class, 'getTeacherReviews']);

// Chat routes
Route::group(['prefix' => 'chat'], function () {
    Route::get('messages', [ChatMessageController::class, 'fetchMessages'])->middleware(['auth:sanctum', 'role:superadmin']);
    Route::post('messages', [ChatMessageController::class, 'sendMessage'])->middleware(['auth:sanctum', 'role:superadmin']);
});
