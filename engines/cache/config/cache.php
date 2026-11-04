<?php

declare(strict_types=1);

return [
    /*| درایور پیش‌فرض: array | file | redis | memcached | apcu | database */
    'default' => \getenv('TVN_CACHE_DRIVER') !== false ? \getenv('TVN_CACHE_DRIVER') : 'array',

    /*| پیشوند کلیدها */
    'prefix' => \getenv('TVN_CACHE_PREFIX') !== false ? \getenv('TVN_CACHE_PREFIX') : 'tvn:',

    /*| TTL پیش‌فرض (ثانیه) */
    'default_ttl' => (int) (\getenv('TVN_CACHE_TTL') !== false ? \getenv('TVN_CACHE_TTL') : 3600),

    /*| مسیر فایل برای FileStore */
    'file_dir' => \getenv('TVN_CACHE_DIR') !== false ? \getenv('TVN_CACHE_DIR') : 'storage/framework/cache',

    /*| تنظیمات tag */
    'tags' => ['enabled' => true],

    /*| Stampede Protection */
    'stampede_protection' => [
        'enabled' => true,
        'lock_ttl' => 10,
        'serve_stale' => true,
    ],

    /*| Lock */
    'lock' => [
        'default_ttl' => 30,
    ],
];
