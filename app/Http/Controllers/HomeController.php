<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Retrieve data from the specified root folder path configured in the application
        $data = $this->controller->listByFolder(config('onedrive.root_folder_path'));

        if (cache('one_drive_root_data') == null) {
            // Retrieve the root folder's web URL and cache it for future use
            $rootFolder = $this->controller->getFolderByPath(config('onedrive.root_folder_path'));
            cache(['one_drive_root_data' => $rootFolder]);
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


    public function activity(Request $request, $fileOrFolderId)
    {
        return $this->controller->getItemActivity($fileOrFolderId);
    }
}
