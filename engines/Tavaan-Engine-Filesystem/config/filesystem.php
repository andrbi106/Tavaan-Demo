<?php

declare(strict_types=1);

return [
    /*| Disk پیش‌فرض */
    'default' => \getenv('TVN_FILESYSTEM_DISK') !== false ? \getenv('TVN_FILESYSTEM_DISK') : 'local',

    /*| Diskهای مختلف */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => \getenv('TVN_STORAGE_PATH') !== false
                ? \getenv('TVN_STORAGE_PATH') . '/app'
                : 'storage/app',
            'visibility' => 'private',
        ],
        'public' => [
            'driver' => 'local',
            'root' => 'storage/app/public',
            'visibility' => 'public',
        ],
        'memory' => [
            'driver' => 'memory',
        ],
    ],

    /*| Symlinkها */
    'links' => [],
];
