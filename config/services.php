<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],
    //google
    'google' => [

    'client_id' => env('GOOGLE_CLIENT_ID'),

    'client_secret' => env('GOOGLE_CLIENT_SECRET'),

    'redirect' => env('GOOGLE_REDIRECT_URI'),

],
// frontend url
'frontend' => [
    'url' => env('FRONTEND_URL'),
],

'pesapal' => [
    'consumer_key'    => env('PESAPAL_CONSUMER_KEY'),
    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
    'base_url'        => env('PESAPAL_BASE_URL', 'https://cybqa.pesapal.com/pesapalv3'),
    'ipn_id'          => env('PESAPAL_IPN_ID'),
],


'yoola' => [
    'url' => env('YOOLA_SMS_URL', 'https://yoolasms.com/api/v1/send'),
    'key' => env('YOOLA_SMS_API_KEY'),
    'sender' => env('YOOLA_SMS_SENDER', 'YoolaSMS'),
],


    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
