<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoogleAuthController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API ROUTE  / AUTH CONTROLLER


// ROUTE FOR AUTH  LOGIN 
Route::post('/registers' , [AuthController::class, 'store']);

// AUTHENTICATE / OR LOGIN 
Route::post('/login' , [AuthController::class, 'authenticate'])->middleware('web');

// WE ARE GETTING THE USERS
Route::middleware(['auth:sanctum'])->group(function() {
     Route::get('/user', [AuthController::class, 'getUser']);

});

// LOGOUT OUT FUNCTION 
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');







// API ROUTE / HOME CONTROLLER 









Route::post('/posts', [HomeController::class, 'createPost'])->middleware('auth:sanctum');
Route::get('/posts', [HomeController::class, 'getPost']);
Route::delete('/posts/{id}', [HomeController::class, 'deletePost'])->middleware('auth:sanctum');
Route::put('/posts/{id}', [HomeController::class, 'update'])->middleware('auth:sanctum');

Route::get('/profile/check', [HomeController::class, 'checkProfile'])->middleware('auth:sanctum');
Route::get('/profile/get', [HomeController::class, 'getProfile'])->middleware('auth:sanctum');
Route::get('/profile/user', [HomeController::class, 'getCurrentUser'])->middleware('auth:sanctum');

Route::post('/profile/setup', [HomeController::class, 'setupProfile'])->middleware('auth:sanctum');

Route::get('/post/view/{id}', [HomeController::class, 'viewPost'])->middleware('auth:sanctum');

Route::get('/profile/post', [HomeController::class, 'historyPost'])->middleware('auth:sanctum');

Route::get('/comment/profile', [HomeController::class, 'profilepicComment'])->middleware('auth:sanctum');

Route::get('profile/photo', [HomeController::class, 'getUserProfilePhoto'])->middleware('auth:sanctum');
Route::post('/post/submit-comment', [HomeController::class, 'makeComment'])->middleware('auth:sanctum');
Route::get('/post/comments', [HomeController::class, 'getComments'])->middleware('auth:sanctum');
Route::get('/post/{id}/total-comments', [HomeController::class, 'totalComments'])->middleware('auth:sanctum');
  // Google OAuth routes
    Route::get('auths/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('auths/google/callback', [GoogleAuthController::class, 'callback'])->middleware('web');



Route::post('/posts/{post}/karma/upvote', [HomeController::class, 'upvote'])->middleware('auth:sanctum');
Route::post('/posts/{post}/karma/downvote', [HomeController::class, 'downvote'])->middleware('auth:sanctum');
Route::post('/posts/{post}/karma/remove', [HomeController::class, 'removeVote'])->middleware('auth:sanctum');
Route::get('/posts/{post}/karma', [HomeController::class, 'show']);