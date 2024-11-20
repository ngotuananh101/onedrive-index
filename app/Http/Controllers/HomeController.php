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

    public function path(Request $request, $path)
    {
        // Get folder metadata by path
        $folderMeta = $this->controller->getFolderByPath(config('onedrive.root_folder_path') . '/' . $path);
        return response()->redirectToRoute('home.folder', ['id' => $folderMeta['id']]);
    }

    public function folder(Request $request, $folderId)
    {
        // Get root folder meta
        $rootFolder = cache('one_drive_root_data');
        $rootPath = $rootFolder['webUrl'];
        // Get metadata for the specified folder
        $folderMeta = $this->controller->getFolderById($folderId);
        $path = $folderMeta['webUrl'];
        $path = str_replace($rootPath, '', $path);
        // Get the folder's content
        $data = $this->controller->listByFolder(config('onedrive.root_folder_path') . $path);
        // Pass the retrieved data to the folder view
        return view('home.index', [
            'data' => $data
        ]);
    }

    public function download(Request $request, $fileId)
    {
        // Get the download URL for the specified file
        $downloadData = $this->controller->getDownloadData($fileId);
        // Redirect the user to the download URL
        return redirect($downloadData['@microsoft.graph.downloadUrl']);
    }
}
