<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use Closure;
use Illuminate\Http\Request;

class CheckOneDriveTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if both access token and refresh token are missing
        if (!cache()->has('one_drive_access_token') && !cache()->has('one_drive_refresh_token')) {
            return redirect()->route('auth.step1');
        }

        // If access token exists, proceed to the next middleware
        if (cache()->has('one_drive_access_token')) {
            config(['filesystems.disks.onedrive.access_token' => cache()->get('one_drive_access_token')]);
            return $next($request);
        }

        // Try to refresh the token if access token is missing
        try {
            $authController = new AuthController();
            $success = $authController->refreshToken();
            if ($success) {
                config(['filesystems.disks.onedrive.access_token' => cache()->get('one_drive_access_token')]);
                return $next($request);
            }
        } catch (\Exception $e) {
            // Log the exception
            \Log::error('Error refreshing OneDrive token: ' . $e->getMessage());
        }

        // Redirect to authentication step 1 if token refresh fails
        return redirect()->route('auth.step1');
    }
}
