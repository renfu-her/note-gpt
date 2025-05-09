<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteFolderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// 公開路由
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

// 需要認證的路由
Route::middleware(['ensure.token'])->group(function () {
    // 認證相關
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 資料夾相關
    Route::get('/folders', [NoteFolderController::class, 'index']);
    Route::post('/folders', [NoteFolderController::class, 'store']);
    Route::put('/folders/{id}', [NoteFolderController::class, 'update']);
    Route::delete('/folders/{id}', [NoteFolderController::class, 'destroy']);
    
    // 筆記相關
    Route::get('/notes/folders/{folder}', [NoteController::class, 'index']); // 特定資料夾的筆記

    // 筆記相關路由
    Route::get('/notes', [NoteController::class, 'index']); // 所有筆記
    Route::get('/notes/{id}', [NoteController::class, 'show']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::post('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
});
