<?php

return [

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    // 'floating_contact_widget' => [
    //     'phone' => env('FLOATING_CONTACT_WIDGET_PHONE', '+85517935151'),
    //     'whatsapp' => env('FLOATING_CONTACT_WIDGET_WHATSAPP', '85517935151'),
    //     'telegram' => env('FLOATING_CONTACT_WIDGET_TELEGRAM', 'yourusername'),
    //     'messenger' => env('FLOATING_CONTACT_WIDGET_MESSENGER', 'yourpage'),
    // ],
    // 'postmark' => [
    //     'token' => env('POSTMARK_TOKEN'),
    // ],

    'bakong' => [
        'api_token' => env('BAKONG_API_TOKEN'),
        'base_url' => env('BAKONG_API_BASE_URL', 'https://api-bakong.nbc.gov.kh'),
        'public_check_url' => env('BAKONG_PUBLIC_CHECK_URL'),
        'public_check_method' => env('BAKONG_PUBLIC_CHECK_METHOD', 'GET'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'speedsms' => [
        'token' => env('SPEEDSMS_TOKEN'),
        'sender' => env('SPEEDSMS_SENDER'),
        'type' => env('SPEEDSMS_TYPE', 2),
    ],
];
