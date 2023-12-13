<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PostController;


//user
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

//profile photo
Route::middleware('auth:api')->group(function () {
    Route::post('/profile/update-profile', [ProfileController::class, 'update_profile']);
});

//post
Route::post('posts/create', [PostController::class, 'create']);
Route::post('posts/delete', [PostController::class, 'delete']);
Route::post('posts/update', [PostController::class, 'update']);
Route::get('posts', [PostController::class, 'posts']);
