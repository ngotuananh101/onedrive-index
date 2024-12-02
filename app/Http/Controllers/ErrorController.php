<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function error($code = null, $title = null, $message = null)
    {
        return view('error.code', [
            'code' => $code,
            'title' => $title,
            'message' => $message
        ]);
    }
}
