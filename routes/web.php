<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/auth.php';
include __DIR__ . '/error.php';

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
    Route::get('/path/{path}', [HomeController::class, 'path'])->name('path')->where('path', '.*');
    Route::get('/folder/{id}', [HomeController::class, 'folder'])->name('folder');
    Route::get('/file/{id}', [HomeController::class, 'file'])->name('file');
    Route::get('/preview/{id}', [HomeController::class, 'preview'])->name('preview');
    Route::get('/download/{id}', [HomeController::class, 'download'])->name('download');
    Route::get('/info/{id}', [HomeController::class, 'info'])->name('info');
    Route::get('/activity/{id}', [HomeController::class, 'activity'])->name('activity');
    Route::post('/search', [HomeController::class, 'search'])->name('search');
    Route::post('/store-password', [HomeController::class, 'storePassword'])->name('store-password');
});
