<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('tickets', TicketController::class)->only(['index', 'store', 'show']);
    Route::post('/tickets/{ticket}/suggest', [TicketController::class, 'suggestResponse']);
    Route::patch('/tickets/{ticket}/close', [TicketController::class, 'close']);
});
