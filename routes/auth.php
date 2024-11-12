<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'auth.',
    'prefix' => 'auth',
], function () {
    Route::get('/step1', [AuthController::class, 'step1'])->name('step1');
});
