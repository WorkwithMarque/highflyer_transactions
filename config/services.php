<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'deco_ntwrk' => [
        'api_key' => env('DECO_NTWRK_API_KEY'),
    ],

    'sjg_print' => [
        'base_url' => env('SJG_PRINT_BASE_URL', 'https://sjgprintdesign.sjgservicesllc.com/api/json'),
        'username' => env('SJG_PRINT_USERNAME', 'ColbyFifer'),
        'password' => env('SJG_PRINT_PASSWORD', 'print12345'),
    ],

    'ghl' => [
        'base_url' => env('GHL_BASE_URL', 'https://rest.gohighlevel.com/v1'),
        'api_key' => env('GHL_API_KEY','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2NhdGlvbl9pZCI6IkcwWW9QVWg0SzBMb3YwTzRoUWdDIiwiY29tcGFueV9pZCI6ImY2Snc2R2k3RTNWYnFmTEJFUmRlIiwidmVyc2lvbiI6MSwiaWF0IjoxNjkyMDM0MzU1MDAwLCJzdWIiOiJ1c2VyX2lkIn0.xTg_l5vZntszEhX2Rehw779ZLT9P8gxNR2lfxmq337I'),
    ],

];
