<?php

declare(strict_types=1);

return [
    /*| مسیر دایرکتوری modules */
    'path' => \getenv('TVN_MODULE_PATH') !== false ? \getenv('TVN_MODULE_PATH') : 'modules',

    /*| ماژول‌های فعال در startup */
    'enabled' => [],

    /*| ماژول‌های غیرفعال */
    'disabled' => [],

    /*| HMVC sub-request */
    'hmvc' => [
        'enabled' => true,
        'cache' => false,
    ],
];
