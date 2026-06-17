<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);     // Mendapatkan semua user
    Route::get('/{id}', [UserController::class, 'show']);  // Mendapatkan detail user berdasarkan ID
    Route::post('/', [UserController::class, 'store']);    // POST user baru
});
