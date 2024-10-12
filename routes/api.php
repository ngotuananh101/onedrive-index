<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OauthController;

Route::get('/oauth/checkLogin', [OauthController::class, 'checkLogin']);
Route::get('/oauth/getToken', [OauthController::class, 'getToken']);
