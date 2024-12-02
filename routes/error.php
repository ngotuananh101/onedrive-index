<?php

use App\Http\Controllers\ErrorController;
use Illuminate\Support\Facades\Route;

Route::get('/5xx', [ErrorController::class, 'error'])->name('error');
