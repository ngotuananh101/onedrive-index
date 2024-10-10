<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function checkLogin()
    {
        $isLogin = cache('is_login', false);
        return response()->json(['is_login' => $isLogin]);
    }

    public function login(Request $request)
    {
        $token = $request->input('token');
        cache(['is_login' => true, 'token' => $token], now()->addMinutes(10));
        return response()->json(['is_login' => true]);
    }
}
