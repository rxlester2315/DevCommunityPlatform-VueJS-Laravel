<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/registers' , [AuthController::class, 'store']);
Route::post('/login' , [AuthController::class, 'authenticate']);

Route::middleware(['auth:sanctum'])->group(function() {
     Route::get('/user', [AuthController::class, 'getUser']);

});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::post('/posts', [HomeController::class, 'createPost'])->middleware('auth:sanctum');
Route::get('/posts', [HomeController::class, 'getPost']);
Route::delete('/posts/{id}', [HomeController::class, 'deletePost'])->middleware('auth:sanctum');
Route::put('/posts/{id}', [HomeController::class, 'update'])->middleware('auth:sanctum');

Route::get('/profile/check', [HomeController::class, 'checkProfile'])->middleware('auth:sanctum');
Route::get('/profile/get', [HomeController::class, 'getProfile'])->middleware('auth:sanctum');
Route::get('/profile/user', [HomeController::class, 'getCurrentUser'])->middleware('auth:sanctum');
Route::post('/profile/setup', [HomeController::class, 'setupProfile'])->middleware('auth:sanctum');

Route::get('/profile/post', [HomeController::class, 'historyPost'])->middleware('auth:sanctum');