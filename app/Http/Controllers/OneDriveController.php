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
            $per_page = max(1, intval($request->input('per_page', 50)));
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                $res = Http::withToken($this->access_token)->get($request->input('next_url'));
            } else {
                $res = Http::withToken($this->access_token)->get($this->endpoint . '/root:/' . env('VITE_ONE_DRIVE_ROOT_FOLDER') . ':/children', [
                    '$top' => $per_page,
                ]);
            }
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];
                $data = [];
                foreach ($value as $key => $v) {
                    $data[] = [
                        'name' => data_get($v, 'name', '-'),
                        'id' => data_get($v, 'id', '-'),
                        'size' => data_get($v, 'size', 0) > 0 ? $this->convertSize($v['size']) : '-',
                        'size_raw' => data_get($v, 'size', 0),
                        'type' => isset($v['folder']) ? 'folder' : 'file',
                        'modified' => isset($v['lastModifiedDateTime']) ? date_create($v['lastModifiedDateTime'])->format('d/m/Y H:i:s') : '-',
                        'created_by' => data_get($v, 'createdBy.user.displayName', '-'),
                        'icon' => isset($v['folder'])
                            ? asset('uploads/images/icon/folder.png')
                            : $this->mapFileTypeImage($v['name']),
                        'download_url' => !isset($v['folder']) ? url('api/drive/download/' . $v['id']) : '-'
                    ];
                }
                return response()->json([
                    'status' => 'success',
                    'next_url' => isset($json['@odata.nextLink']) ? $json['@odata.nextLink'] : null,
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

    public function getFolderById(Request $request, $id)
    {
        try {
            $per_page = max(1, intval($request->input('per_page', 50)));
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                $res = Http::withToken($this->access_token)->get($request->input('next_url'));
            } else {
                $res = Http::withToken($this->access_token)->get($this->endpoint . '/items/' . $id . '/children', [
                    '$top' => $per_page,
                ]);
            }
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];
                $data = [];
                foreach ($value as $key => $v) {
                    $data[] = [
                        'name' => data_get($v, 'name', '-'),
                        'id' => data_get($v, 'id', '-'),
                        'size' => data_get($v, 'size', 0) > 0 ? $this->convertSize($v['size']) : '-',
                        'size_raw' => data_get($v, 'size', 0),
                        'type' => isset($v['folder']) ? 'folder' : 'file',
                        'modified' => isset($v['lastModifiedDateTime']) ? date_create($v['lastModifiedDateTime'])->format('d/m/Y H:i:s') : '-',
                        'created_by' => data_get($v, 'createdBy.user.displayName', '-'),
                        'icon' => isset($v['folder'])
                            ? asset('uploads/images/icon/folder.png')
                            : $this->mapFileTypeImage($v['name']),
                        'download_url' => !isset($v['folder']) ? url('api/drive/download/' . $v['id']) : '-',
                    ];
                }
                return response()->json([
                    'status' => 'success',
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

    public function breadcrumb($id)
    {
        try {
            $parent = [];
            $currentPath = "";
            $rootPath = env('VITE_ONE_DRIVE_ROOT_FOLDER') == "/" ? '/drive/root:/' : '/drive/root:/' . env('VITE_ONE_DRIVE_ROOT_FOLDER');
            $loop = 1;
            do {
                $res = Http::withToken($this->access_token)->get($this->endpoint . '/items/' . $id);
                if ($res->status() === 200) {
                    $json = $res->json();
                    if ($loop == 1) {
                        $current = $json['name'];
                    }
                    $parentReference = $json['parentReference'];
                    // Add parent details to the parent array
                    $parent[] = [
                        'name' => $parentReference['name'],
                        'id' => $parentReference['id'],
                    ];
                    // Update currentPath for the next loop iteration
                    $currentPath = $parentReference['path'];
                    $id = $parentReference['id'];
                } else {
                    // Break the loop if an error occurs while fetching parent details
                    break;
                }
                $loop++;
            } while ($currentPath !== $rootPath);
            return response()->json([
                'status' => 'success',
                'current_folder_name' => $current,
                'data' => array_reverse($parent),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function download(Request $request, $id)
    {
        try {
            // Get item details
            $res = Http::withToken($this->access_token)->get($this->endpoint . '/items/' . $id);

            if ($res->status() === 200) {
                $json = $res->json();
                $name = data_get($json, 'name', 'downloaded_file');

                // Stream file to browser
                $client = new \GuzzleHttp\Client();
                $response = $client->get($this->endpoint . '/items/' . $id . '/content', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->access_token,
                    ],
                    'stream' => true,
                ]);

                return response()->streamDownload(function () use ($response) {
                    $body = $response->getBody();
                    while (!$body->eof()) {
                        echo $body->read(1024);
                    }
                }, $name);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found or access denied.',
                ], $res->status());
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
