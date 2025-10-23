<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('landing-page');
});

Route::get('auths/google/callback/web', [GoogleAuthController::class, 'callbackWeb'])->name('google.callback.web');


Route::get('/{pathMatch}',function(){
    return view('landing-page');
})->where('pathMatch','.*');