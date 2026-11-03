<?php
declare(strict_types=1);
return [
    'timeout' => (int) (\getenv('TVN_HTTP_TIMEOUT') !== false ? \getenv('TVN_HTTP_TIMEOUT') : 30),
    'default_headers' => [],
    'retry' => ['times' => 1, 'delay_ms' => 100],
    'verify_tls' => true,
];
