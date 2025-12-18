<?php

// Cấu hình credentials cho các dịch vụ bên thứ ba (Mailgun, SES, Google, Facebook, Stripe...).
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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'vnd_to_usd_rate' => env('STRIPE_VND_TO_USD_RATE', 25000), // Default: 1 USD = 25,000 VND (fallback if API fails)
    ],

    'exchange_rate' => [
        'api_key' => env('EXCHANGE_RATE_API_KEY'), // Optional: For ExchangeRate-API.com (free tier: 1,500 requests/month)
        'enabled' => env('EXCHANGE_RATE_API_ENABLED', true), // Enable/disable API fetching
        'cache_duration' => env('EXCHANGE_RATE_CACHE_DURATION', 3600), // Cache duration in seconds (default: 1 hour)
    ],

];
