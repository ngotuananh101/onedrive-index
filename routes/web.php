<?php

use App\Http\Controllers\HomeController;
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
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/next-page', [HomeController::class, 'getNextPage'])->name('next-page');
    Route::get('/folder/{id}', [HomeController::class, 'folder'])->name('folder');
    Route::get('/file/{id}', [HomeController::class, 'file'])->name('file');
});