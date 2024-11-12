<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOnedriveTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (cache()->has('onedrive_access_token')) {
            return $next($request);
        } else {
            return redirect()->route('auth.step1');
        }
    }
}
