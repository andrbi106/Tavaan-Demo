<?php

declare(strict_types=1);

return [
    /*| درایور پیش‌فرض */
    'driver' => \getenv('TVN_HASH_DRIVER') !== false ? \getenv('TVN_HASH_DRIVER') : 'argon2id',

    /*| تنظیمات Argon2id */
    'argon2id' => [
        'memory_cost' => (int) (\getenv('TVN_HASH_MEMORY_COST') !== false ? \getenv('TVN_HASH_MEMORY_COST') : 65536),
        'time_cost' => (int) (\getenv('TVN_HASH_TIME_COST') !== false ? \getenv('TVN_HASH_TIME_COST') : 4),
        'threads' => (int) (\getenv('TVN_HASH_THREADS') !== false ? \getenv('TVN_HASH_THREADS') : 1),
    ],

    /*| تنظیمات Bcrypt (fallback) */
    'bcrypt' => [
        'cost' => (int) (\getenv('TVN_HASH_BCRYPT_COST') !== false ? \getenv('TVN_HASH_BCRYPT_COST') : 12),
    ],

    /*| rehash خودکار هنگام ورود */
    'rehash_on_login' => \getenv('TVN_HASH_REHASH_ON_LOGIN') !== false
        ? \filter_var(\getenv('TVN_HASH_REHASH_ON_LOGIN'), \FILTER_VALIDATE_BOOL)
        : true,
];
