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

// Create A Post 
Route::post('/posts', [HomeController::class, 'createPost'])->middleware('auth:sanctum');

// Display The Created Post
Route::get('/posts', [HomeController::class, 'getPost']);
// Delete Created Post
Route::delete('/posts/{id}', [HomeController::class, 'deletePost'])->middleware('auth:sanctum');
// Update Post 
Route::put('/posts/{id}', [HomeController::class, 'update'])->middleware('auth:sanctum');



// UPDATE PROFILE 

//Check if the Current User Have Profile
Route::get('/profile/check', [HomeController::class, 'checkProfile'])->middleware('auth:sanctum');
// Display Profile Data , When Visiting Profile
Route::get('/profile/get', [HomeController::class, 'getProfile'])->middleware('auth:sanctum');
// Get User Data Current Auth
Route::get('/profile/user', [HomeController::class, 'getCurrentUser'])->middleware('auth:sanctum');
// Setup Profile for no profile user
Route::post('/profile/setup', [HomeController::class, 'setupProfile'])->middleware('auth:sanctum');
// We getting profile picture of Profile pictutre
Route::get('profile/photo', [HomeController::class, 'getUserProfilePhoto'])->middleware('auth:sanctum');





// POST RELATED 

// VIEW POSTS 
Route::get('/post/view/{id}', [HomeController::class, 'viewPost'])->middleware('auth:sanctum');
// IN PROFILE WE SHOW LISTED POST HE MADE
Route::get('/profile/post', [HomeController::class, 'historyPost'])->middleware('auth:sanctum');

// we store the comment from user
Route::post('/post/submit-comment', [HomeController::class, 'makeComment'])->middleware('auth:sanctum');
// display all comment in post 
Route::get('/post/comments', [HomeController::class, 'getComments'])->middleware('auth:sanctum');
// we display   total comment in post
Route::get('/post/{id}/total-comments', [HomeController::class, 'totalComments'])->middleware('auth:sanctum');


// THIS ROUTE IS FOR VOTING FEATURES

Route::post('/posts/{post}/karma/upvote', [HomeController::class, 'upvote'])->middleware('auth:sanctum');
Route::post('/posts/{post}/karma/downvote', [HomeController::class, 'downvote'])->middleware('auth:sanctum');
Route::post('/posts/{post}/karma/remove', [HomeController::class, 'removeVote'])->middleware('auth:sanctum');
Route::get('/posts/{post}/karma', [HomeController::class, 'show']);






// Google OAuth routes
Route::get('auths/google/redirect', [GoogleAuthController::class, 'redirect']);

Route::get('auths/google/callback', [GoogleAuthController::class, 'callback'])->middleware('web');