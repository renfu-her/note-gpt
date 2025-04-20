<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoteFolderController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

// 公開路由
Route::post('/login', [AuthController::class, 'login']);

// 需要認證的路由
Route::middleware('auth:member')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 資料夾路由
    Route::get('/folders', [NoteFolderController::class, 'index']);
    
    // 筆記路由
    Route::get('/notes', [NoteController::class, 'index']);
});
