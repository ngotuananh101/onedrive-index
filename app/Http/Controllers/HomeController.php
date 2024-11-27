<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    /**
     * The OneDriveController instance to be used within this class.
     *
     * @var OneDriveController
     */
    public OneDriveController $controller;

    /**
     * Constructor for the HomeController class.
     *
     * @param OneDriveController $controller The OneDrive controller instance to be used within this class.
     */
    public function __construct(OneDriveController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Index method for the home page
     *
     * This method retrieves data from a specified folder in OneDrive and passes it to the view for display.
     */
    public function index()
    {
        // Check if the root folder data is cached
        if (cache('one_drive_root_data') == null) {
            // Retrieve the root folder's web URL and cache it for future use
            $rootFolder = $this->controller->getFolderByPath(config('onedrive.root_folder_path'));
            cache(['one_drive_root_data' => $rootFolder]);
        } else {
            // Retrieve the root folder data from the cache
            $rootFolder = cache('one_drive_root_data');
        }

        // Retrieve data from the specified root folder path configured in the application
        $data = $this->controller->listByFolder(config('onedrive.root_folder_path'));

        // Check folder is protected
        $check = $this->checkProtected(request(), $rootFolder);
        if ($check) {
            return $check;
        }

        // Pass the retrieved data to the home index view
        return view('home.index', [
            'data' => $data
        ]);
    }

    /**
     * Fetches the next page of content.
     *
     * This method delegates the request to the OneDriveController to handle pagination logic.
     *
     * @param Request $request The HTTP request object containing pagination parameters.
     *
     * @return mixed The response from the OneDriveController's getNextPage method, typically the next set of paginated data.
     */
    public function getNextPage(Request $request)
    {
        // Delegate the request to the OneDriveController and return its response
        return $this->controller->getNextPage($request);
    }

    /**
     * Redirects to the folder page based on the provided path
     *
     * This function handles the situation where a folder path is provided, and it needs to redirect to the corresponding folder page.
     * It first calculates the metadata of the folder based on the path, and then redirects to the folder page using the obtained folder ID.
     *
     * @param Request $request The incoming request object, not used in this function, but is required by the Laravel framework routing method.
     * @param string $path The path of the target folder, used to locate the folder.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the folder page.
     */
    public function path(Request $request, $path)
    {
        try {
            // Get folder metadata by path
            $folderMeta = $this->controller->getFolderByPath(config('onedrive.root_folder_path') . '/' . $path);
            // Check if have error
            if (isset($folderMeta['error'])) {
                throw new \Exception('Cannot get folder.');
            }
            // Redirect to the folder page using the obtained folder ID
            return response()->redirectToRoute('home.folder', ['id' => $folderMeta['id']]);
        } catch (\Throwable $th) {
            abort(500, __('Can not get folder path.'));
        }
    }

    /**
     * Retrieves and displays the contents of a specified folder
     *
     * This function handles the retrieval of metadata for a specified folder in OneDrive, including the folder's contents,
     * and passes this information to the front-end for display. It first obtains the root folder's metadata from the cache,
     * then retrieves the metadata for the specified folder using its ID. After obtaining the folder's content, it passes
     * this data to the designated view for rendering.
     *
     * @param Request $request The request object, not used in this function but is required by the framework
     * @param string $folderId The unique identifier of the folder
     *
     * @return \Illuminate\View\View Returns the rendered view with the folder's content
     */
    public function folder(Request $request, $folderId)
    {
        try {
            // Get root folder meta
            $rootFolder = cache('one_drive_root_data');
            $rootPath = $rootFolder['webUrl'];

            // Get metadata for the specified folder
            $folderMeta = $this->controller->getFolderById($folderId);

            // Check if there was an error
            if (isset($folderMeta['error'])) {
                throw new \Exception('Cannot get folder.');
            }

            // Check if the folder is protected
            $check = $this->checkProtected($request, $folderMeta);
            if ($check) {
                return $check;
            }

            $path = $folderMeta['webUrl'];
            $path = str_replace($rootPath, '', $path);

            // Get the folder's content
            $data = $this->controller->listByFolder(config('onedrive.root_folder_path') . $path);

            if (isset($data['error'])) {
                throw new \Exception('Cannot get folder content.');
            }

            // Pass the retrieved data to the folder view
            return view('home.index', [
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            abort(500, __('Cannot get folder content.'));
        }
    }

    /**
     * Handles file display requests.
     *
     * This method is responsible for retrieving file information based on the provided file ID,
     * and processing different handling logic based on the file type. If the file is a folder,
     * it redirects to the folder view. If the file is an MP4 video, it attempts to find and
     * prepare subtitle information. For other file types, it prepares a preview URL.
     *
     * @param Request $request The HTTP request object, not directly used in this method, but is available for potential use.
     * @param int $fileId The unique identifier of the file to be processed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View Redirects to the appropriate view with the file information.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Throws a 500 error response if file information cannot be retrieved.
     */
    public function file(Request $request, $fileId)
    {
        try {
            // Get the download URL for the specified file
            $fileInfo = $this->controller->getItemInfoById($fileId, true);
            // Check if there was an error
            if (isset($fileInfo['error'])) {
                throw new \Exception('Cannot get file info.');
            }
            // check if file is folder
            if (isset($fileInfo['folder'])) {
                return response()->redirectToRoute('home.folder', ['id' => $fileId]);
            }

            // Check if the file is in a protected folder
            $check = $this->checkProtected($request, $fileInfo);
            if ($check) {
                return $check;
            }

            // Check file is mp4 video
            $fileName = $fileInfo['name'];
            if (Str::endsWith($fileName, '.mp4')) {
                // Get subtitle file based on video file name
                $currentPath = $fileInfo['parentReference']['path'];
                $currentPath = str_replace('/drive/root:', '', $currentPath);
                $currentPath = $currentPath . '/' . str_replace('.mp4', '.vtt', $fileName);
                $subTitle = $this->controller->getItemInfoByPath($currentPath, true);
                if (!isset($subTitle['error'])) {
                    $fileInfo['subTitle'] = [
                        'name' => $subTitle['name'],
                        'downloadUrl' => $subTitle['@microsoft.graph.downloadUrl']
                    ];
                    $fileInfo['customPreview'] = true;
                }
            } else {
                $fileInfo['preview'] = route('home.preview', ['id' => $fileId]);
            }

            // Pass the retrieved data to the file view
            return view('home.file', [
                'file' => $fileInfo,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            abort(500, __('Can not get file info.'));
        }
    }

    /**
     * Handles file download requests
     *
     * This method is responsible for processing file download requests initiated by users. It receives a file identifier,
     * retrieves the file's download URL, and then redirects the user to that URL to start the download.
     *
     * @param Request $request The HTTP request object, not directly used in this method, but is part of the method signature
     * @param mixed $fileId The unique identifier for the file to be downloaded
     *
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the file's download URL
     */
    public function download(Request $request, $fileId)
    {
        try {
            // Get the download URL for the specified file
            $downloadData = $this->controller->getDownloadData($fileId);
            // Check if there was an error
            if (isset($downloadData['error'])) {
                throw new \Exception('Cannot get folder content.');
            }
            // Redirect the user to the download URL
            return redirect($downloadData['@microsoft.graph.downloadUrl']);
        } catch (\Throwable $th) {
            //throw $th;
            abort(500, __('Can not download file.'));
        }
    }

    /**
     * Retrieves the metadata of a specified file or folder based on its ID
     *
     * @param Request $request The incoming request object, not used in this method but is a common parameter for controller actions
     * @param mixed $fileOrFolderId The ID of the file or folder, used to retrieve the corresponding metadata
     *
     * @return \Illuminate\Http\JsonResponse Returns the metadata of the specified file or folder in JSON format
     */
    public function info(Request $request, $fileOrFolderId)
    {
        // Get the metadata for the specified file or folder
        return $this->controller->getItemInfoById($fileOrFolderId);
    }


    /**
     * Get the activity records for a specified file or folder.
     *
     * This method retrieves the activity information for a specific file or folder by calling the `getItemActivity` method of the controller.
     *
     * @param \Illuminate\Http\Request $request The user request object containing request-related information.
     * @param mixed $fileOrFolderId The identifier of the file or folder for which to retrieve activity records.
     *
     * @return mixed The result returned by the `getItemActivity` method of the controller, typically containing activity record information.
     */
    public function activity(Request $request, $fileOrFolderId)
    {
        return $this->controller->getItemActivity($fileOrFolderId);
    }

    /**
     * Executes a search operation
     *
     * This method processes a search request, trims and slugs the query, and then either retrieves the result from cache
     * or performs a search using the OneDriveController. The results are cached for 60 minutes to improve performance.
     *
     * @param Request $request The HTTP request object containing the search query and pagination parameters
     * @return mixed The search results, typically an array of items matching the query
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $query = trim($query);
        $queryKey = Str::slug($query);
        $page = $request->input('page', 1);
        $cacheKey = 'search_' . $queryKey . '_' . $page;

        // Check if the search result is already cached
        if (cache()->has($cacheKey)) {
            $result = cache()->get($cacheKey);
        } else {
            // Perform the search using the OneDriveController
            $result = $this->controller->search($query, $page);
            // Cache the search result for 60 minutes
            cache()->put($cacheKey, $result, 60);
        }

        return $result;
    }

    /**
     * Generates a preview for a file.
     * Attempts to retrieve and process the file preview, removing any branding elements before returning the processed HTML.
     * If the preview cannot be retrieved or an error occurs, a default empty preview page is returned.
     *
     * @param string $fileId The unique identifier of the file for which to generate the preview.
     * @return \Illuminate\Http\Response Returns an HTTP response containing the HTML content of the preview.
     */
    public function preview($fileId)
    {
        // Define an empty preview HTML to be used when an error occurs or the preview cannot be retrieved.
        $empty = '<html><head></head><body style="display: flex; justify-content: center; align-items: center;"><h3>Cannot get preview</h3></body></html>';

        try {
            // Attempt to retrieve the file preview using the controller.
            $preview = $this->controller->getPreview($fileId);

            // If there is no error in retrieving the preview, proceed with processing.
            if (!isset($preview['error'])) {
                // Retrieve the file preview URL.
                $fileInfo['preview'] = $preview['getUrl'];

                // Fetch the content of the preview page.
                $content = file_get_contents($preview['getUrl']);

                // Initialize DOMDocument for parsing and modifying the HTML content.
                $dom = new \DOMDocument();
                // Use error suppression to ignore warnings or errors when loading HTML.
                @$dom->loadHTML($content);

                // Initialize DOMXPath for querying HTML elements.
                $xpath = new \DOMXPath($dom);

                // Add custom CSS style to hide branding elements.
                $style = $dom->createElement('style', '.od-Branding {display: none;}');

                // Get the <head> element of the HTML document.
                $head = $dom->getElementsByTagName('head')->item(0);

                // Append the custom style element to the <head> section.
                $head->appendChild($style);

                // Remove elements with the 'od-Branding' class to hide branding.
                $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' od-Branding ')]");
                foreach ($elements as $element) {
                    // Remove each branding element from the DOM.
                    $element->parentNode->removeChild($element);
                }

                // Save the modified HTML content.
                $data = $dom->saveHTML();

                // Return the processed HTML content as an HTTP response with the appropriate Content-Type header.
                return response($data)->header('Content-Type', 'text/html');
            } else {
                // If an error is encountered while retrieving the preview, return the empty preview page.
                return response($empty)->header('Content-Type', 'text/html');
            }
        } catch (\Throwable $th) {
            // If an exception occurs, return the empty preview page.
            return response($empty)->header('Content-Type', 'text/html');
        }
    }

    public function checkProtected(Request $request, $fileOrFolderInfo)
    {
        $protected = config('onedrive.protected');
        if (isset($fileOrFolderInfo['folder'])) {
            $currentPath = $fileOrFolderInfo['parentReference']['path'] . '/' . $fileOrFolderInfo['name'];
        } else {
            $currentPath = $fileOrFolderInfo['parentReference']['path'];
        }
        $currentPath = str_replace('/drive/root:', '', $currentPath);
        $isProtected = false;
        foreach ($protected as $path => $password) {
            if (str_contains($currentPath, $path)) {
                $isProtected = true;
                $currentPath = $path;
                break;
            }
        }
        if ($isProtected) {
            $password = $protected[$currentPath];
            $cookieKey = 'protected_' . md5($currentPath);
            $inputPassword = isset($_COOKIE[$cookieKey]) ? $_COOKIE[$cookieKey] : null;
            if ($inputPassword == $password) {
                return false;
            } else {
                return view('home.protected', [
                    'path' => $currentPath
                ]);
            }
        } else {
            return false;
        }
    }

    public function storePassword(Request $request)
    {
        try {
            $path = $request->input('path');
            $password = $request->input('password');

            return response()->json([
                'status' => 'success',
                'cookie' => [
                    'name' => 'protected_' . md5($path),
                    'value' => $password,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => __('Cannot store password.')
            ]);
        }
    }
}
