<?php

/**
 * تَوان | Tavaan — autoload config (Demo)
 *
 * نگاشت namespaceها برای autoloader تَوان.
 * این فایل توسط core/bootstrap.php خوانده می‌شود.
 */

return [
    'psr-4' => [
        // هستهٔ فریمورک
        'Tavaan\\Core\\'    => ['core/'],
        'Tavaan\\Support\\' => ['core/Support/'],

        // موتور والد
        'Tavaan\\EngineCore\\' => ['engine-core/'],

        // موتورهای فرزند (wildcard)
        'Tavaan\\Engine\\'  => ['engines/*/src/'],
        'Tavaan\\Testing\\' => ['engines/test/src/'],

        // اپلیکیشن
        'App\\'             => ['app/src/'],
    ],
    'classmap' => [],
    'files' => [
        'core/Support/helpers.php',
    ],
    'exclude' => [
        'tests/*', 'storage/*',
    ],
    'cache' => [
        'enabled' => ($_SERVER['TVN_AUTOLOAD_CACHE'] ?? 'false') === 'true',
        'file'    => 'storage/cache/classmap.php',
    ],
    'locked_prefixes' => [
        'Tavaan\\Core\\',
        'Tavaan\\Support\\',
        'Tavaan\\EngineCore\\',
        'Tavaan\\Engine\\',
    ],
];
