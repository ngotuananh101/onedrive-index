<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'auth.',
    'prefix' => 'auth',
], function () {
    Route::get('/step1', [AuthController::class, 'step1'])->name('step1');
    Route::get('/step2', [AuthController::class, 'step2'])->name('step2');
    Route::get('/callback', [AuthController::class, 'callback'])->name('callback');
    Route::get('/step3', [AuthController::class, 'step3'])->name('step3');
});
