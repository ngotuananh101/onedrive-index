<?php

return [
    'auth' => [
        'step1' => [
            'title' => 'Step 1/3: Preparations',
            'recommendation' => 'For best performance, we recommend using a REDIS as cache driver. <a href="https://laravel.com/docs/11.x/redis" target="_blank" class="link text-sm">Learn more</a>',
            'description' => 'Authorization is required as no valid access_token or refresh_token is present on this deployed instance. Check the following configurations before proceeding with your own Microsoft account.',
            'if_incorrect' => 'If you see anything incorrect, please change the following values in your .env file or onedrive config and try again.',
            'process_to_oauth' => 'Process to OAuth',
        ],
        'step2' => [
            'title' => 'Step 2/3: Get authorization code',
            'not_owner' => 'If you are not the owner of this website, stop now, as continuing with this process may expose your personal files in OneDrive.',
            'link_created' => 'The OAuth link for getting the authorization code has been created. Click on the button after to get the authorization code. Your browser will redirect to Microsoft\'s account login page. After logging in and authenticating with your Microsoft account, you will be redirected to step 3 if successful.',
        ],
    ],
];
