<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS (httpSMS)
    |--------------------------------------------------------------------------
    | Driver supports: httpsms, log (dev/null), disabled
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'enabled' => env('SMS_ENABLED', false),
        'simulate' => env('SMS_SIMULATE', false),

        'httpsms' => [
            'api_key' => env('HTTPSMS_KEY') ?: env('HTTPSMS_API_KEY'),
            'from' => env('HTTPSMS_FROM'),
            'base_url' => env('HTTPSMS_BASE_URL', 'https://api.httpsms.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Assistant (OpenRouter / Gemini)
    |--------------------------------------------------------------------------
    | Driver supports: openrouter, gemini, disabled
    */
    'ai' => [
        'driver' => env('AI_DRIVER', 'openrouter'),
        'enabled' => env('AI_ENABLED', false),

        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'model' => env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
            'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'google/gemini-2.5-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        ],
    ],

];