<?php
declare(strict_types=1);
return [
    'trusted_proxies' => [],
    'trusted_headers' => [],
    'middleware' => [],
    'compress' => \getenv('TVN_HTTP_COMPRESS') !== false ? \filter_var(\getenv('TVN_HTTP_COMPRESS'), \FILTER_VALIDATE_BOOL) : false,
];
