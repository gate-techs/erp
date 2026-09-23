<?php

return [
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'gemini'),
    'log_usage' => env('AI_LOG_USAGE', false),

    'providers' => [
        'gemini' => [
            'driver' => 'gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'timeout' => (int) env('GEMINI_TIMEOUT', 30),
        ],

        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'timeout' => (int) env('OPENAI_TIMEOUT', 30),
        ],

        'deepl' => [
            'driver' => 'deepl',
            'api_key' => env('DEEPL_API_KEY'),
            'base_url' => env('DEEPL_BASE_URL', 'https://api-free.deepl.com/v2'),
            'timeout' => (int) env('DEEPL_TIMEOUT', 30),
        ],
    ],
];
