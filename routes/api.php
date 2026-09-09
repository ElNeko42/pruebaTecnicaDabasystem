<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomTypeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/front/room-types', [RoomTypeController::class, 'publicIndex']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('admin/room-types', RoomTypeController::class)->only(['index', 'store', 'update']);
});
