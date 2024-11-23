<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneDriveController extends Controller
{
    /**
     * The number of items to display per page.
     */
    protected int $PER_PAGE;

    /**
     * Access token used for authentication.
     */
    protected string $ACCESS_TOKEN;

    /**
     * URL of the user API for sending requests.
     */
    protected string $USER_API_URL;

    /**
     * Constructor for the OneDriveController class.
     * Initializes the controller with default settings and retrieves necessary configurations.
     */
    public function __construct()
    {
        // Set the number of items to display per page
        $this->PER_PAGE = 20;

        // Retrieve the access token from the cache
        $this->ACCESS_TOKEN = cache('one_drive_access_token');

        // Set the URL for the OneDrive user API from configuration
        $this->USER_API_URL = config('onedrive.user_api_url');
    }

    /**
     * Convert file size to a more readable format.
     *
     * This function converts the given file size into a more understandable format by choosing
     * an appropriate unit (e.g., Bytes B, Kilobytes KB, Megabytes MB, etc.) and returns a formatted string.
     * If the file size is 0, it returns '-' to indicate no data.
     *
     * @param int $size The file size in bytes.
     * @return string The formatted file size with the appropriate unit.
     */
    public function convertSize($size)
    {
        // Return '-' if the file size is 0 to indicate no data
        if ($size == 0) {
            return '-';
        } else {
            // Define the units array from Bytes B to Petabytes PB
            $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            // Loop to divide the file size by 1024 until it is less than 1024 to determine the correct unit
            for ($i = 0; $size > 1024; $i++) {
                $size /= 1024;
            }
            // Return the formatted file size with the appropriate unit
            return round($size, 2) . ' ' . $unit[$i];
        }
    }

    /**
     * Get the icon for a file based on its extension.
     *
     * This function determines the appropriate icon to represent a file by checking its file extension.
     * It uses a switch statement to match the file extension against known types and assigns the corresponding icon class.
     * If the file extension does not match any of the known types, a default icon is returned.
     *
     * @param string $path The file path from which the file extension will be extracted.
     * @return string The CSS class representing the file icon.
     */
    public function getFileIcon($path)
    {
        // Extract the file extension from the given path
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Set a default icon
        $icon = 'far fa-file';

        // Determine the icon based on the file extension
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

        // Return the determined icon
        return $icon;
    }

    /**
     * Retrieves a list of files and folders within a specified path on OneDrive.
     *
     * This function makes an HTTP GET request to the OneDrive API to retrieve information about the files and folders
     * located at the given path. It also generates breadcrumb navigation for the path.
     *
     * @param string $path The path of the folder whose contents are to be listed.
     * @return array An array containing information about the items in the folder, breadcrumb navigation, the current path,
     *               the next page URL (if available), and the owner information.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Redirects to a 500 error page if an exception occurs.
     */
    public function listByFolder($path)
    {
        try {
            // Get the root folder path
            $root = config('onedrive.root_folder_path');
            // Decode the URL to get the original path
            $path = urldecode($path);
            // Make an authenticated GET request to the OneDrive API to retrieve information about the files and folders in the specified path
            $response = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/root:{$path}:/children?select=id,name,size,file,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime,parentReference&top={$this->PER_PAGE}");
            // Check if the request was successful
            if ($response->successful()) {
                // Parse the response data
                $data = $response->json();
                // Extract the list of items
                $items = $data['value'];
                // Initialize breadcrumb navigation array
                $breadcrumbs = [];
                // If the current path is not the root folder, generate breadcrumb navigation
                if ($path !== $root) {
                    // Remove the root path to get the path from the root
                    $pathFromRoot = str_replace($root, '', $path);
                    // Split the path into folder names
                    $folders = explode('/', $pathFromRoot);
                    // Initialize the folder path string
                    $folderPath = '';
                    // Iterate through each folder name to generate breadcrumb navigation
                    foreach ($folders as $folder) {
                        if (!empty($folder)) {
                            // Append the current folder name to the folder path string
                            $folderPath .= $folder . '/';
                            // Add the current breadcrumb to the breadcrumb navigation array
                            $breadcrumbs[] = [
                                'name' => $folder,
                                'path' => $folderPath,
                            ];
                        }
                    }
                }

                // Return the list of items, breadcrumb navigation, current path, next page URL (if available), and owner information
                return [
                    'items' => $items,
                    'breadcrumbs' => $breadcrumbs,
                    'path' => $path,
                    'next_url' => $data['@odata.nextLink'] ?? null,
                    'owner' => cache('one_drive_user')
                ];
            } else {
                throw new \Exception('Error getting folder content');
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder content: ' . $th->getMessage());
            // Redirect to the home page
            abort(500, __('Error getting folder content'));
        }
    }

    /**
     * Get the next page of items
     *
     * This function is used to retrieve the next page of items when browsing OneDrive resources. It processes the request based on whether a next page URL is provided,
     * decodes the HTML entities in the URL, makes an HTTP request to obtain data, processes the data, and returns the processed item list and the URL of the next page.
     * If there is no next page URL or the request fails, it throws an exception and returns an error response.
     *
     * @param Request $request The request object, used to get the next page URL
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing the status, data, and next page URL
     */
    public function getNextPage(Request $request)
    {
        try {
            // Get the cached OneDrive user information
            $owner = cache('one_drive_user');

            // Check if the next page URL exists and is not empty
            if ($request->has('next_url') && !empty($request->input('next_url'))) {
                // Get the next page URL from the request and decode the HTML entities to get the correct URL
                $nextUrl = $request->next_url;
                $nextUrl = html_entity_decode($nextUrl);

                // Make an HTTP GET request to the next page URL with the access token
                $response = Http::withToken($this->ACCESS_TOKEN)->get($nextUrl);

                // Check if the request was successful
                if ($response->successful()) {
                    // Parse the response JSON data
                    $data = $response->json();
                    $items = $data['value'];

                    // Process each item, adding icon, size, last modified time, owner information, and operation buttons
                    $items = array_map(function ($item) use ($owner) {
                        // Set the item icon based on whether it is a folder or a file
                        $item['icon'] = isset($item['folder']) ? 'far fa-folder' : $this->getFileIcon($item['name']);
                        // Set the item size, display "-" for folders
                        $item['size'] = isset($item['folder'])  ? '-' : $this->convertSize($item['size']);
                        // Convert the last modified time to the format Y-m-d H:i:s
                        $item['lastModifiedDateTime'] = date('Y-m-d H:i:s', strtotime($item['lastModifiedDateTime']));
                        // Set the item owner information
                        $item['owner'] = '<div class="flex items-center avatar">
                                    <div class="w-[25px] h-[25px] rounded-full">
                                        <img src="' . $owner['photo'] . '" />
                                    </div>
                                    <span class="ml-2">' . $owner['displayName'] . '</span>
                                </div>';
                        // Set the item view link based on whether it is a folder or a file
                        $viewLink = isset($item['folder']) ? route('home.folder', ['id' => $item['id']]) : route('home.file', ['id' => $item['id']]);
                        $item['link'] = $viewLink;
                        // Set the item view button
                        $item['viewBtn'] = '<a href="' . $viewLink . '">' . __('View') . '</a>';
                        // Set the item download button if the download URL exists
                        if (isset($item['@microsoft.graph.downloadUrl'])) {
                            $item['downloadBtn'] = '<a href="' . route('home.download', $item['id']) . '" target="_blank">' . __('Download') . '</a>';
                        } else {
                            $item['downloadBtn'] = '';
                        }
                        return $item;
                    }, $items);

                    // Return the processed item list and the URL of the next page
                    return response()->json([
                        'status' => 'success',
                        'data' => $items,
                        'next_url' => $data['@odata.nextLink'] ?? null
                    ]);
                } else {
                    throw new \Exception(__('Can not get next page'));
                }
            } else {
                // If the next page is not available, throw an exception
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

    /**
     * Retrieves folder information by folder ID.
     *
     * This function sends a GET request to the user API endpoint to retrieve the folder information.
     * It uses the HTTP client to make the request and includes the access token in the request header for authentication.
     * If the request is successful, it returns the folder data in JSON format. If an error occurs during the request,
     * it logs the error and returns an empty array.
     *
     * @param string $folderId The ID of the folder to retrieve.
     * @return array Folder data in array format, or an empty array if an error occurs.
     */
    public function getFolderById($folderId)
    {
        try {
            // Send a GET request to the user API endpoint to retrieve folder information
            $response = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/items/{$folderId}");
            // Check if the request was successful
            if ($response->successful()) {
                // Convert the response to JSON format
                $data = $response->json();
                // Return the folder data
                return $data;
            } else {
                // Handle the error
                throw new \Exception('Error fetching folder data');
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder: ' . $th->getMessage());
            // return error
            return [
                'error' => true
            ];
        }
    }

    /**
     * Retrieves folder information by path.
     *
     * This function attempts to retrieve folder information from a remote server using an HTTP GET request.
     * It includes the access token in the request for authentication. If the request succeeds, it returns
     * the JSON data of the folder. If an error occurs during the request, it logs the error and returns an
     * empty array.
     *
     * @param string $path The path of the folder to retrieve.
     * @return array|boolean Folder information in JSON format or false.
     */
    public function getFolderByPath($path)
    {
        try {
            // Send a GET request to the server with the access token
            $response = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/root:{$path}");
            // Check if the request was successful
            if ($response->successful()) {
                // Convert the response content to JSON format
                $data = $response->json();
                // Return the parsed JSON data
                return $data;
            } else {
                throw new \Exception('Error getting folder');
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting folder: ' . $th->getMessage());
            // return error
            return [
                'error' => true
            ];
        }
    }

    /**
     * Retrieves download data for a file.
     *
     * This method makes a GET request to the Microsoft Graph API to fetch the download URL and other relevant information for a specified file.
     * It returns an associative array containing the file's ID, name, and download URL.
     * If the request fails or there is an issue parsing the response, it logs the error and returns an empty array.
     *
     * @param string $fileId The unique identifier of the file for which to retrieve the download data.
     *
     * @return array An associative array containing the file's ID, name, and download URL, or an empty array if an error occurs.
     */
    public function getDownloadData($fileId)
    {
        try {
            // Make a GET request to the Microsoft Graph API with the access token
            $response = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/items/{$fileId}?select=id,name,@microsoft.graph.downloadUrl");

            // Parse the JSON response
            $data = $response->json();

            // Return the parsed data
            return $data;
        } catch (\Throwable $th) {
            // Log the error message
            Log::error('Error getting download URL: ' . $th->getMessage());

            // Return an empty array in case of an error
            return [
                'error' => true
            ];
        }
    }

    /**
     * Get item information by item ID
     *
     * This function retrieves the details of an item by its ID from a remote server.
     * It attempts to fetch both the item's metadata and its thumbnail.
     * If the thumbnail is unavailable, a default image is used.
     * Additionally, it formats the item's creation and modification dates, and converts the size for better readability.
     *
     * @param mixed $itemId Item ID, used to identify the item on the server
     * @return array Returns an array containing the item's details, including the thumbnail URL, formatted dates, and converted size
     */
    public function getItemInfoById($itemId)
    {
        try {
            // Get thumbnail, metadata
            $thumbnail = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/items/{$itemId}/thumbnails/0/medium");
            $response = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/items/{$itemId}");
            // check response is success
            if ($response->successful()) {
                $data = $response->json();
                // Check if thumbnail URL exists, assign either the retrieved URL or a default image URL
                if (isset($thumbnail['url'])) {
                    $data['thumbnail'] = $thumbnail['url'];
                } else {
                    $data['thumbnail'] = asset('assets/media/png/folder.png');
                }
                // Format dates
                $data['createdDateTime'] = Carbon::parse($data['createdDateTime'])->format('Y-m-d H:i:s');
                $data['lastModifiedDateTime'] = Carbon::parse($data['lastModifiedDateTime'])->format('Y-m-d H:i:s');
                // Convert size
                $data['size'] = $this->convertSize($data['size']);
                // Return the processed data
                return response()->json($data);
            } else {
                throw new \Exception('Error processing request');
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting item info: ' . $th->getMessage());
            // return
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting item info'
            ], 500);
        }
    }

    /**
     * Get the activity information of a specific item
     *
     * This function retrieves the activity information of a specified item by making an HTTP GET request to the user API. It uses the access token for authentication.
     *
     * @param string $itemId The unique identifier of the item for which to retrieve activity information
     * @return mixed Returns the activity information of the item on success, or an empty array on failure
     */
    public function getItemActivity($itemId)
    {
        try {
            // Use HTTP with the access token to get the item's activity information
            $itemActivity = Http::withToken($this->ACCESS_TOKEN)->get("{$this->USER_API_URL}/drive/items/{$itemId}/activities");
            // check if the request was successful
            if ($itemActivity->successful()) {
                $data = $itemActivity->json();
                // return response()->json($data);
                $value = $data['value'];
                $response = [];

                foreach ($value as $item) {
                    $action = $item['action'];
                    $actionKeys = array_keys($action);
                    $actor = $item['actor']['user']['displayName'];
                    $action = [
                        __('Was'),
                        __($actionKeys[0]),
                        __('by'),
                        $actor
                    ];
                    $response[] = [
                        'time' => Carbon::parse($item['times']['recordedDateTime'])->format('Y-m-d H:i:s'),
                        'action' => implode(' ', $action),
                    ];
                }
                return response()->json($response);
            } else {
                throw new \Exception('Error');
            }
        } catch (\Throwable $th) {
            // Log the error
            Log::error('Error getting item activity: ' . $th->getMessage());

            // return
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting item info'
            ], 500);
        }
    }
}
