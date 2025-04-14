<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReviewController;


Route::get('/availabilities/{id}', [AvailabilityController::class, 'index']);

Route::post('/availabilities/{id}', [AvailabilityController::class, 'storeEvents']);

Route::get('/exceptions/{id}', [ExceptionController::class, 'index']);

Route::post('/exceptions/{id}', [ExceptionController::class, 'storeExceptions']);

Route::put('/exceptions/{id}', [ExceptionController::class, 'update']);

Route::delete('/exceptions/{id}', [ExceptionController::class, 'destroy']);

Route::get('/map/teachers', [MapController::class, 'index']);

Route::post('/reviews', [ReviewController::class, 'saveReview']);

// Chat routes
Route::group(['prefix' => 'chat'], function () {
    Route::get('messages', [ChatMessageController::class, 'fetchMessages']);
    Route::post('messages', [ChatMessageController::class, 'sendMessage']);
});
