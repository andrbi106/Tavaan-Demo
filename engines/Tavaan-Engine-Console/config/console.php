<?php

declare(strict_types=1);

return [
    /*| مسیر commandهای خودکشف */
    'paths' => [
        \getenv('TVN_CONSOLE_PATH') !== false ? \getenv('TVN_CONSOLE_PATH') : 'src/Console',
    ],

    /*| رنگ‌ها در خروجی */
    'colors' => true,

    /*| حالت verbose پیش‌فرض */
    'verbose' => \getenv('TVN_VERBOSE') !== false
        ? \filter_var(\getenv('TVN_VERBOSE'), \FILTER_VALIDATE_BOOL)
        : false,
];
