<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $controller = new OneDriveController();
        $data = $controller->listByFolder(config(('onedrive.root_folder_path')));
        return view('home.index', [
            'data' => $data
        ]);
    }

    public function getNextPage(Request $request)
    {
        $controller = new OneDriveController();
        return $controller->getNextPage($request);
    }
}
