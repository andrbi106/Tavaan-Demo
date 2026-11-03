<?php

declare(strict_types=1);

return [
    /*| env handling */
    'env_file' => \getenv('TVN_ENV_FILE') !== false ? \getenv('TVN_ENV_FILE') : '.env',
    'env_required' => \getenv('TVN_ENV_REQUIRED') !== false
        ? \filter_var(\getenv('TVN_ENV_REQUIRED'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| config caching */
    'cache' => [
        'enabled' => \getenv('TVN_CONFIG_CACHE') !== false
            ? \filter_var(\getenv('TVN_CONFIG_CACHE'), \FILTER_VALIDATE_BOOL)
            : false,
        'path' => 'storage/framework/config.php',
    ],

    /*| schema validation in DEV */
    'schema' => [
        'enabled' => \getenv('TVN_CONFIG_SCHEMA') !== false
            ? \filter_var(\getenv('TVN_CONFIG_SCHEMA'), \FILTER_VALIDATE_BOOL)
            : false,
    ],

    /*| Frozen در production */
    'frozen' => \getenv('TVN_CONFIG_FROZEN') !== false
        ? \filter_var(\getenv('TVN_CONFIG_FROZEN'), \FILTER_VALIDATE_BOOL)
        : false,
];
