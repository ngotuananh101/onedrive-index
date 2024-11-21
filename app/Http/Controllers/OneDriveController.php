<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneDriveController extends Controller
{
    protected int $PER_PAGE;
    protected string $ACCESS_TOKEN;

    public function __construct()
    {
        $this->PER_PAGE = 20;
        $this->ACCESS_TOKEN = cache('one_drive_access_token');
    }

    public function convertSize($size)
    {
        if ($size == 0) {
            return '-';
        } else {
            $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            for ($i = 0; $size > 1024; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . ' ' . $unit[$i];
        }
    }

    public function getFileIcon($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $icon = 'far fa-file';
        switch ($extension) {
            case 'pdf':
                $icon = 'far fa-file-pdf file-pdf';
                break;
            case 'doc':
            case 'docx':
                $icon = 'far fa-file-word file-word';
                break;
            case 'xls':
            case 'xlsx':
                $icon = 'far fa-file-excel file-excel';
                break;
            case 'ppt':
            case 'pptx':
                $icon = 'far fa-file-powerpoint file-powerpoint';
                break;
            case 'zip':
            case 'rar':
                $icon = 'far fa-file-archive file-archive';
                break;
            case 'mp3':
            case 'wav':
                $icon = 'far fa-file-audio file-audio';
                break;
            case 'mp4':
            case 'avi':
            case 'mov':
                $icon = 'far fa-file-video file-video';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $icon = 'far fa-file-image file-image';
                break;
            case 'php':
            case 'html':
            case 'css':
            case 'js':
                $icon = 'far fa-file-code file-code';
                break;
        }

        return $icon;
    }

    public function listByFolder($path)
    {
        try {
            $root = config('onedrive.root_folder_path');
            $path = urldecode($path);
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/root:{$path}:/children?select=id,name,size,file,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime,parentReference&top={$this->PER_PAGE}");
            $data = $response->json();
            $items = $data['value'];
            $breadcrumbs = [];
            if ($path !== $root) {
                $pathFromRoot = str_replace($root, '', $path);
                $folders = explode('/', $pathFromRoot);
                $folderPath = '';
                foreach ($folders as $folder) {
                    if (!empty($folder)) {
                        $folderPath .= $folder . '/';
                        $breadcrumbs[] = [
                            'name' => $folder,
                            'path' => $folderPath,
                        ];
                    }
                }
            }
            return [
                'items' => $items,
                'breadcrumbs' => $breadcrumbs,
                'path' => $path,
                'next_url' => $data['@odata.nextLink'] ?? null,
                'owner' => cache('one_drive_user')
            ];
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder content: ' . $th->getMessage());
            // Redirect to the home page
            abort(500, __('Error getting folder content'));
        }
    }

    public function getNextPage(Request $request)
    {
        try {
            $owner = cache('one_drive_user');
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                $nextUrl = $request->next_url;
                // Parse the next URL from html to get the correct URL
                $nextUrl = html_entity_decode($nextUrl);
                $response = Http::withToken($this->ACCESS_TOKEN)->get($nextUrl);
                $data = $response->json();
                $items = $data['value'];
                $items = array_map(function ($item) use ($owner) {
                    $item['icon'] = isset($item['folder']) ? 'far fa-folder' : $this->getFileIcon($item['name']);
                    $item['size'] = isset($item['folder'])  ? '-' : $this->convertSize($item['size']);
                    $item['lastModifiedDateTime'] = date('Y-m-d H:i:s', strtotime($item['lastModifiedDateTime']));
                    $item['owner'] = '<div class="flex items-center avatar">
                                    <div class="w-[25px] h-[25px] rounded-full">
                                        <img src="' . $owner['photo'] . '" />
                                    </div>
                                    <span class="ml-2">' . $owner['displayName'] . '</span>
                                </div>';
                    $viewLink = isset($item['folder']) ? route('home.folder', ['id' => $item['id']]) : route('home.file', ['id' => $item['id']]);
                    $item['link'] = $viewLink;
                    $item['viewBtn'] = '<a href="' . $viewLink . '">' . __('View') . '</a>';
                    if (isset($item['@microsoft.graph.downloadUrl'])) {
                        $item['downloadBtn'] = '<a href="' . route('home.download', $item['id']) . '" target="_blank">' . __('Download') . '</a>';
                    } else {
                        $item['downloadBtn'] = '';
                    }
                    return $item;
                }, $items);
                return response()->json([
                    'status' => 'success',
                    'data' => $items,
                    'next_url' => $data['@odata.nextLink'] ?? null
                ]);
            } else {
                throw new \Exception(__('Next page is not available'));
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting root folder: ' . $th->getMessage());
            // Response with error
            return response()->json([
                'status' => 'error',
                'message' => __('Can not get next page'),
                'data' => []
            ]);
        }
    }

    public function getFolderById($folderId)
    {
        try {
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/items/{$folderId}");
            $data = $response->json();
            return $data;
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder: ' . $th->getMessage());
            // return empty array
            return [];
        }
    }

    public function getFolderByPath($path)
    {
        try {
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/root:{$path}");
            $data = $response->json();
            return $data;
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder: ' . $th->getMessage());
            // return empty array
            return [];
        }
    }

    public function getDownloadData($fileId)
    {
        try {
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/items/{$fileId}?select=id,name,@microsoft.graph.downloadUrl");
            $data = $response->json();
            return $data;
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting download URL: ' . $th->getMessage());
            // return empty array
            return [];
        }
    }

    public function getChildFolderList($folderId)
    {
        try {
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/items/{$folderId}/children?select=id,name,folder,parentReference");
            $data = $response->json();
            return $data['value'];
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting child folders: ' . $th->getMessage());
            // return empty array
            return [];
        }
    }

    public function getItemInfoById($itemId)
    {
        try {
            // Get thumbnail, metadata
            $thumbnail = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/items/{$itemId}/thumbnails/0/medium");
            $response = Http::withToken($this->ACCESS_TOKEN)->get("https://graph.microsoft.com/v1.0/me/drive/items/{$itemId}");
            $data = $response->json();
            if (isset($thumbnail['url'])) {
                $data['thumbnail'] = $thumbnail['url'];
            } else {
                $data['thumbnail'] = asset('assets/media/png/folder.png');
            }
            return $data;
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting item info: ' . $th->getMessage());
            // return empty array
            return [];
        }
    }
}
