<?php

declare(strict_types=1);

return [
    /*| Fake mode (برای تست) */
    'fake' => \getenv('TVN_EVENT_FAKE') !== false
        ? \filter_var(\getenv('TVN_EVENT_FAKE'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| wildcard default */
    'wildcard_default' => '*',

    /*| Discovery — اسکن خودکار listenerها از attributes */
    'discovery' => [
        'enabled' => \getenv('TVN_EVENT_DISCOVERY') !== false
            ? \filter_var(\getenv('TVN_EVENT_DISCOVERY'), \FILTER_VALIDATE_BOOL)
            : false,
        'cache' => 'storage/framework/events.php',
    ],
];
