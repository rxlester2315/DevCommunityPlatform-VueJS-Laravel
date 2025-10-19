<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing-page');
});




Route::get('/{pathMatch}',function(){
    return view('landing-page');
})->where('pathMatch','.*');