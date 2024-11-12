<?php

use Illuminate\Support\Facades\Route;

include __DIR__ . '/auth.php';

Route::group([
    'as' => 'home.',
    'middleware' => ['checkOnedriveToken']
], function () {
    Route::get('/', function () {
        return view('home.index');
    })->name('index');
});
