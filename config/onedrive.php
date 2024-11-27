<?php

return [
    'client_id' => env('ONEDRIVE_CLIENT_ID'),
    'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
    'redirect_uri' => env('ONEDRIVE_REDIRECT_URI'),
    'auth_api_url' => env('ONEDRIVE_AUTH_API_URL'),
    'token_api_url' => env('ONEDRIVE_TOKEN_API_URL'),
    'user_api_url' => env('ONEDRIVE_USER_API_URL'),
    'search_api_url' => env('ONEDRIVE_SEARCH_API_URL'),
    'scope' => env('ONEDRIVE_SCOPE'),
    'root_folder_path' => env('ONEDRIVE_ROOT_FOLDER_PATH'),
    'date_format' => env('ONEDRIVE_DATE_FORMAT', 'Y-m-d H:i:s'),
    'social' => [
        'facebook' => [
            'icon' => 'fab fa-facebook',
            'url' => 'https://www.facebook.com/',
        ],
        'github' => [
            'icon' => 'fab fa-github',
            'url' => 'https://github.com/',
        ],
    ],
    // Set protected folder with password key is folder path and value is password use / for root folder and /folder for sub folder
    'protected' => [
        '/Share/Private' => 'TuanAnh2101',
    ],
];
