<?php

// config/bakong.php

return [
    // NOTE: this file is already namespaced under "bakong" by Laravel itself
    // (the filename becomes the top-level config key). Do NOT add another
    // 'bakong' => [...] wrapper inside here — that was the bug: it made the
    // real path config('bakong.bakong.api_token') instead of the expected
    // config('bakong.api_token'), so every call to config('bakong.api_token')
    // silently returned null even though .env had the correct value.

    'api_token' => env('BAKONG_API_TOKEN'),
    'base_url' => env('BAKONG_API_BASE_URL', 'https://api-bakong.nbc.gov.kh'),
    'public_check_url' => env('BAKONG_PUBLIC_CHECK_URL'),
    'public_check_method' => env('BAKONG_PUBLIC_CHECK_METHOD', 'GET'),

    // Also referenced by BakongKHQRService's constructor — keep these here
    // too so account_id/merchant_name/merchant_city/currency resolve the
    // same way (matches what generateKHQR()/the constructor expect).
    'account_id' => env('BAKONG_ACCOUNT_ID', env('KHPAY_BAKONG_ID')),
    'merchant_name' => env('BAKONG_MERCHANT_NAME', env('KHPAY_MERCHANT_NAME', 'My Shop')),
    'merchant_city' => env('BAKONG_MERCHANT_CITY', env('KHPAY_MERCHANT_CITY', 'PHNOMPENH')),
    'currency' => env('BAKONG_CURRENCY', env('KHPAY_CURRENCY', 840)),
];
