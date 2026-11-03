<?php
declare(strict_types=1);
return [
    'allowed_origins' => ['*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => [],
    'supports_credentials' => false,
    'max_age' => 86400,
];
