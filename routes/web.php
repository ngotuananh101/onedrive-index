<?php

use Illuminate\Support\Facades\Route;

include __DIR__ . '/auth.php';

Route::get('/language/{lang}', function ($lang) {
    cache()->forget('language');
    cache()->put('language', $lang);
    return redirect()->back();
})->name('language');

Route::group([
    'as' => 'home.',
    'middleware' => ['checkOneDriveToken']
], function () {
    Route::get('/', function () {
        return view('home.index');
    })->name('index');
});
