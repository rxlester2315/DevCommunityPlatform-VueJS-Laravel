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