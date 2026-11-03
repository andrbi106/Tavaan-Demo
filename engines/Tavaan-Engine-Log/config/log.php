<?php

declare(strict_types=1);

return [
    /*| سطح پیش‌فرض لاگ */
    'level' => \getenv('TVN_LOG_LEVEL') !== false ? \getenv('TVN_LOG_LEVEL') : 'debug',

    /*| کانال پیش‌فرض */
    'default' => \getenv('TVN_LOG_CHANNEL') !== false ? \getenv('TVN_LOG_CHANNEL') : 'default',

    /*| Handlerها */
    'handlers' => [
        ['type' => 'stdout', 'format' => 'line'],
        [
            'type' => 'file',
            'path' => \getenv('TVN_LOG_FILE') !== false ? \getenv('TVN_LOG_FILE') : 'storage/logs/app.log',
            'format' => \getenv('TVN_LOG_FORMAT') !== false ? \getenv('TVN_LOG_FORMAT') : 'json',
            'append' => true,
        ],
    ],

    /*| masking — پنهان‌کردن کلیدهای حساس در context */
    'mask_keys' => ['password', 'token', 'api_key', 'secret', 'credit_card'],

    /*| چرخش فایل */
    'rotation' => [
        'enabled' => \getenv('TVN_LOG_ROTATE') !== false
            ? \filter_var(\getenv('TVN_LOG_ROTATE'), \FILTER_VALIDATE_BOOL)
            : true,
        'max_files' => 7,  // روز
        'max_size_mb' => 100,
    ],

    /*| buffering */
    'buffer' => [
        'enabled' => \getenv('TVN_LOG_BUFFER') !== false
            ? \filter_var(\getenv('TVN_LOG_BUFFER'), \FILTER_VALIDATE_BOOL)
            : false,
        'size' => 100,
    ],
];
