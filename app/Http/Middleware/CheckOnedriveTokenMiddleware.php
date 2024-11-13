<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOneDriveTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!cache()->has('one_drive_access_token') && !cache()->has('one_drive_refresh_token')) {
            return redirect()->route('auth.step1');
        } else {
            if (cache()->has('one_drive_access_token')) {
                return $next($request);
            } else {
                return redirect()->route('auth.step3');
            }
        }
    }
}
