<?php
declare(strict_types=1);
return [
    'metrics' => [
        'enabled' => true,
        'prefix' => \getenv('TVN_METRICS_PREFIX') !== false ? \getenv('TVN_METRICS_PREFIX') : 'tavaan_',
    ],
    'tracing' => [
        'enabled' => true,
        'sample_rate' => 1.0,
    ],
    'health' => [
        'enabled' => true,
    ],
];
