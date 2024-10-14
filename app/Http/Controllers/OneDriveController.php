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

    public function getRoot(Request $request)
    {
        try {
            $page = max(1, intval($request->input('page', 1)));
            $per_page = max(1, intval($request->input('per_page', 50)));

            $res = Http::withToken($this->access_token)->get($this->endpoint . '/root/children');
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];

                // Calculate offset and limit for pagination
                $offset = ($page - 1) * $per_page;
                $limit = $per_page;

                $data = [];
                $count = 0;
                foreach ($value as $key => $v) {
                    if ($count >= $offset && $count < $offset + $limit) {
                        $data[] = [
                            'name' => data_get($v, 'name', '-'),
                            'id' => data_get($v, 'id', '-'),
                            'size' => data_get($v, 'size', 0) > 0 ? $this->convertSize($v['size']) : '-',
                            'type' => isset($v['folder']) ? 'folder' : 'file',
                            'modified' => isset($v['lastModifiedDateTime']) ? date_create($v['lastModifiedDateTime'])->format('d/m/Y H:i:s') : '-',
                            'created_by' => data_get($v, 'createdBy.user.displayName', '-'),
                            'icon' => isset($v['folder'])
                                ? asset('uploads/images/icon/folder.png')
                                : $this->mapFileTypeImage($v['name'])
                        ];
                    }
                    $count++;
                }

                return response()->json([
                    'status' => 'success',
                    'total' => count($value),
                    'data' => $data,
                ], 200);
            } else {
                abort(500, 'Failed to retrieve file list');
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

    public function mapFileTypeImage($file_name)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $is_exits = file_exists(public_path('uploads/images/icon/' . $ext . '.png'));
        return $is_exits ? asset('uploads/images/icon/' . $ext . '.png') : asset('uploads/images/icon/file.png');
    }
}
