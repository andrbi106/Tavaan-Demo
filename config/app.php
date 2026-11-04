<?php

/**
 * تَوان | Tavaan — Application Config (Demo v0.0.1)
 */

return [
    'name' => 'Tavaan Demo',
    'version' => '0.0.1',
    'env' => env('TVN_APP_ENV', 'development'),
    'debug' => env('TVN_APP_DEBUG', true),
    'timezone' => 'Asia/Tehran',
    'locale' => 'fa',
    'fallback_locale' => 'en',
    'key' => env('TVN_APP_KEY', ''),
    'url' => env('TVN_APP_URL', 'http://localhost:8000'),
];
