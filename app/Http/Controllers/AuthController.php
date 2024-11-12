<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
    public function step1()
    {
        return view('auth.step1');
    }
}
