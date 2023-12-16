<?php

use App\Models\Comment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;


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
    Route::post('/profile/get-profile', [ProfileController::class, 'get_profile']);
});

//post
Route::post('posts/create', [PostController::class, 'create']);
Route::post('posts/delete', [PostController::class, 'delete']);
Route::post('posts/update', [PostController::class, 'update']);
Route::get('posts', [PostController::class, 'posts']);

//comment
Route::post('comments/create', [CommentController::class, 'create']);
Route::post('comments/delete', [CommentController::class, 'delete']);
Route::post('comments/update', [CommentController::class, 'update']);
Route::post('posts/comments', [CommentController::class, 'comments']);

//like
Route::post('posts/like', [LikeController::class, 'like']);

//upload photo post
Route::post('save_user_info', [AuthController::class, 'saveUserInfo']);

