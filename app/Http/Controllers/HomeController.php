<?php

namespace App\Http\Controllers;

use Storage;

class HomeController extends Controller
{
    public function index()
    {
        $listCurrentFolder = Storage::disk('onedrive')->directories(config('onedrive.root_folder_path'));
        $listCurrentFile = Storage::disk('onedrive')->files(config('onedrive.root_folder_path'));
        $data = [];
        foreach ($listCurrentFolder as $folder) {
            $name = explode('\\', $folder);
            $size = $this->convertsize(Storage::disk('onedrive')->size($folder));
            $lastModified = Storage::disk('onedrive')->lastModified($folder);
            $data[] = [
                'name' => end($name),
                'owner' => cache('one_drive_user'),
                'size' => $size,
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'path' => $folder,
                'download' => null
            ];
        }
        foreach ($listCurrentFile as $file) {
            $name = explode('\\', $file);
            $size = $this->convertsize(Storage::disk('onedrive')->size($file));
            $lastModified = Storage::disk('onedrive')->lastModified($file);
            $data[] = [
                'name' => end($name),
                'owner' => cache('one_drive_user'),
                'size' => $size,
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'path' => $file,
                'download' => route('home.download', ['path' => $file])
            ];
        }
        return view('home.index', [
            'data' => $data
        ]);
    }

    public function convertsize($size)
    {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $unit[$i];
    }

    public function download($path)
    {
        return Storage::disk('onedrive')->download($path);
    }
}
