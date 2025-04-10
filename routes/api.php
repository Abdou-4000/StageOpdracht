<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\Api\ChatMessageController;

Route::get('/availabilities', [AvailabilityController::class, 'index']);

Route::post('/availabilities', [AvailabilityController::class, 'storeEvents']);

Route::get('/exceptions', [ExceptionController::class, 'index']);

Route::post('/exceptions', [ExceptionController::class, 'storeExceptions']);

Route::put('/exceptions/{id}', [ExceptionController::class, 'update']);

Route::delete('/exceptions/{id}', [ExceptionController::class, 'destroy']);

// Chat routes
Route::group(['prefix' => 'chat'], function () {
    Route::get('messages', [ChatMessageController::class, 'fetchMessages']);
    Route::post('messages', [ChatMessageController::class, 'sendMessage']);
});