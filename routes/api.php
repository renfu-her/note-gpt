<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

// 公開路由
Route::post('/login', [AuthController::class, 'login']);

// 需要認證的路由
Route::middleware('auth:sanctum')->group(function () {
    // 認證相關
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 資料夾相關
    Route::get('/folders', [FolderController::class, 'index']);
    
    // 筆記相關
    Route::get('/notes', [NoteController::class, 'index']);
});
