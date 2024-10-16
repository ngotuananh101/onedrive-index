<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneDriveController extends Controller
{
    // Declare class properties for storing API endpoint and tokens
    public mixed $endpoint;
    public mixed $refresh_token;
    public mixed $access_token;

    // Constructor to initialize endpoint and tokens from cache and environment variables
    public function __construct()
    {
        $this->endpoint = env('VITE_ONE_DRIVE_API_URL');
        $this->refresh_token = cache('one_drive_refresh_token');
        $this->access_token = cache('one_drive_access_token');

        // Refresh access token if it's not available and refresh token is present
        if (!$this->access_token && $this->refresh_token) {
            $check = (new OauthController())->refreshToken();
            if ($check) {
                $this->access_token = cache('one_drive_access_token');
            }
        }
    }

    // Method to get root folder contents
    public function getRoot(Request $request)
    {
        try {
            // Set per_page value with a default of 50 and a minimum of 1
            $per_page = max(1, intval($request->input('per_page', 50)));

            // Check if next_url is provided to continue pagination
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                $res = Http::withToken($this->access_token)->get($request->input('next_url'));
            } else {
                $res = Http::withToken($this->access_token)->get($this->endpoint . '/root:/' . env('VITE_ONE_DRIVE_ROOT_FOLDER') . ':/children', [
                    '$top' => $per_page,
                ]);
            }

            // Handle successful response
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];
                $data = [];

                // Process each item in the response
                foreach ($value as $v) {
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

                // Return successful response
                return response()->json([
                    'status' => 'success',
                    'next_url' => isset($json['@odata.nextLink']) ? $json['@odata.nextLink'] : null,
                    'data' => $data,
                ], 200);
            } else {
                // Handle unsuccessful response
                abort(500, 'Failed to retrieve file list');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting root folder: ' . $e->getMessage());
            // Handle exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Method to get contents of a folder by ID
    public function getFolderById(Request $request, $id)
    {
        try {
            // Set per_page value with a default of 50 and a minimum of 1
            $per_page = max(1, intval($request->input('per_page', 50)));

            // Check if next_url is provided to continue pagination
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                $res = Http::withToken($this->access_token)->get($request->input('next_url'));
            } else {
                $res = Http::withToken($this->access_token)->get($this->endpoint . '/items/' . $id . '/children', [
                    '$top' => $per_page,
                ]);
            }

            // Handle successful response
            if ($res->status() === 200) {
                $json = $res->json();
                $value = $json['value'];
                $data = [];

                // Process each item in the response
                foreach ($value as $v) {
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

                // Return successful response
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                ], 200);
            } else {
                // Handle unsuccessful response
                abort(500, 'Failed to retrieve file list');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting folder by id: ' . $e->getMessage());
            // Handle exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Method to generate breadcrumb navigation for a folder
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

            // Return successful response
            return response()->json([
                'status' => 'success',
                'current_folder_name' => $current,
                'data' => array_reverse($parent),
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting breadcrumb: ' . $e->getMessage());
            // Handle exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Method to get a preview URL for a file
    public function preview($id)
    {
        try {
            $res = Http::withToken($this->access_token)->post($this->endpoint . '/items/' . $id . '/preview');
            if ($res->status() === 200) {
                $json = $res->json();
                return response()->json([
                    'status' => 'success',
                    'data' => $json['getUrl'],
                ], 200);
            } else {
                abort(500, 'Failed to retrieve file preview');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting preview url: ' . $e->getMessage());
            // Handle exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function download(Request $request, $id)
    {
        set_time_limit(0);
        try {
            // Get item details from OneDrive using the item ID
            $res = Http::withToken($this->access_token)->get($this->endpoint . '/items/' . $id);
            if ($res->status() === 200) {
                $json = $res->json();
                $download_url = $json['@microsoft.graph.downloadUrl'];
                return response()->redirectTo($download_url);
            } else {
                abort($res->status(), 'Failed to download file');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting download file: ' . $e->getMessage());
            // Handle exception
            abort(500, $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        try {
            // Prepare the search data
            $search_data = [
                "requests" => [
                    [
                        "entityTypes" => ["driveItem"],
                        "query" => [
                            "queryString" => $query
                        ],
                        "from" => 0,
                        "size" => 100,
                        "scopes" => [env('VITE_ONE_DRIVE_SEARCH_SCOPE')],
                        "fields" => [
                            "id",
                            "name",
                            "size",
                            "mimeType",
                            "fileType",
                            "parentLink"
                        ]
                    ],
                ]
            ];

            // Perform the search request to OneDrive
            $res = Http::withToken($this->access_token)->post(env('VITE_ONE_DRIVE_SEARCH_API_URL'), $search_data);
            if ($res->status() === 200) {
                $json = $res->json();
                $total = $json['value'][0]['hitsContainers'][0]['total'];
                $data_file = [];
                $data_folder = [];

                if ($total > 0) {
                    // Process the search results
                    foreach ($json['value'][0]['hitsContainers'][0]['hits'] as $k => $v) {
                        $type = $v['resource']['listItem']['fields']['mimeType'] == 'Folder' ? 'folder' : 'file';
                        $da = [
                            'id' => $v['resource']['id'],
                            'name' => $v['resource']['name'],
                            'size' => $this->convertSize($v['resource']['size']),
                            'type' => $type,
                            'path' => '/' . env('VITE_ONE_DRIVE_ROOT_FOLDER') . str_replace(env('VITE_ONE_DRIVE_SEARCH_SCOPE'), '', $v['resource']['listItem']['fields']['parentLink'])
                        ];
                        if ($type == 'folder') {
                            $data_folder[] = $da;
                        } else {
                            $data_file[] = $da;
                        }
                    }
                }

                // Return the search results
                return response()->json([
                    'status' => 'success',
                    'data_file' => $data_file,
                    'data_folder' => $data_folder,
                    'query' => $query,
                    'total' => $total
                ], 200);
            } else {
                abort(500, 'Failed to retrieve search results');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error searching: ' . $e->getMessage());
            // Handle exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function convertSize($size)
    {
        // Convert file size to a human-readable format
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function mapFileTypeImage($file_name)
    {
        // Map file extension to a corresponding icon image
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $is_exists = file_exists(public_path('uploads/images/icon/' . $ext . '.png'));
        return $is_exists ? asset('uploads/images/icon/' . $ext . '.png') : asset('uploads/images/icon/file.png');
    }
}
