<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\OneDriveController;

Route::get('/oauth/checkLogin', [OauthController::class, 'checkLogin']);
Route::get('/oauth/getToken', [OauthController::class, 'getToken']);

Route::group(['prefix' => 'drive'], function () {
    Route::get('/root', [OneDriveController::class, 'getRoot']);
    Route::get('/folder/{id}', [OneDriveController::class, 'getFolderById']);
    Route::get('/download/{id}', [OneDriveController::class, 'download']);
    Route::get('/breadcrumb/{id}', [OneDriveController::class, 'breadcrumb']);
    Route::get('/preview/{id}', [OneDriveController::class, 'preview']);
});
