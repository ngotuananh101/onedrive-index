<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OneDriveController extends Controller
{
    public mixed $endpoint;
    public mixed $access_token;
    public function __construct()
    {
        $this->endpoint = env('VITE_ONE_DRIVE_API_URL');
        $this->access_token = cache('one_drive_access_token');
    }

    public function getRoot()
    {
        try {
            $res = Http::withToken($this->access_token)->get($this->endpoint . '/root/children');
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];
                dd($value);
                $data = [];
                foreach ($value as $key => $v) {
                    $data[] = [
                        'name' => $v['name'],
                        'id' => $v['id'],
                        'size' => $v['size'] > 0 ? $this->convertSize($v['size']) : '-',
                        'type' => isset($v['folder']) ? 'folder' : 'file',
                    ];
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                ], 200);
            } else {
                throw new \Exception('Lỗi khi lấy danh sách file');
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function convertSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < 4) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
