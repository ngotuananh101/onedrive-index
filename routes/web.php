<?php

use Illuminate\Support\Facades\Route;

Route::get('/oath/login', function () {
    return '123';
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
