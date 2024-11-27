<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get language from cache
        $language = cache()->get('language', 'en');
        app()->setLocale($language);
        // Get theme mode from cookie
        $theme = $request->cookie('theme-mode');
        if ($theme) {
            session()->put('theme-mode', $theme);
        }
        dd($theme);
        return $next($request);
    }
}
