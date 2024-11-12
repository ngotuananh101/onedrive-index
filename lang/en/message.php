<?php

return [
    'auth' => [
        'step1' => [
            'title' => 'Step 1/3: Preparations',
            'recommendation' => 'For best performance, we recommend using a REDIS as cache driver. <a href="https://laravel.com/docs/11.x/redis" target="_blank" class="link text-sm">Learn more</a>',
            'description' => 'Authorisation is required as no valid access_token or refresh_token is present on this deployed instance. Check the following configurations before proceeding with your own Microsoft account.',
            'if_incorrect' => 'If you see anything incorrect, please change the following values in your .env file or onedrive config and try again.',
        ]
    ],
];
