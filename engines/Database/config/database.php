<?php
declare(strict_types=1);
return [
    'default' => \getenv('TVN_DB_CONNECTION') !== false ? \getenv('TVN_DB_CONNECTION') : 'default',
    'connections' => [
        'default' => [
            'driver' => \getenv('TVN_DB_DRIVER') !== false ? \getenv('TVN_DB_DRIVER') : 'sqlite',
            'database' => \getenv('TVN_DB_DATABASE') !== false ? \getenv('TVN_DB_DATABASE') : ':memory:',
            'host' => \getenv('TVN_DB_HOST') !== false ? \getenv('TVN_DB_HOST') : '127.0.0.1',
            'port' => (int) (\getenv('TVN_DB_PORT') !== false ? \getenv('TVN_DB_PORT') : 3306),
            'username' => \getenv('TVN_DB_USERNAME') !== false ? \getenv('TVN_DB_USERNAME') : null,
            'password' => \getenv('TVN_DB_PASSWORD') !== false ? \getenv('TVN_DB_PASSWORD') : null,
            'charset' => 'utf8mb4',
        ],
    ],
    'query_log' => [
        'enabled' => \getenv('TVN_DB_LOG') !== false ? \filter_var(\getenv('TVN_DB_LOG'), \FILTER_VALIDATE_BOOL) : false,
        'slow_threshold' => (float) (\getenv('TVN_DB_SLOW') !== false ? \getenv('TVN_DB_SLOW') : 1.0),
    ],
    'read_write_split' => false,
];
