<?php
declare(strict_types=1);
return [
    'connection' => \getenv('TVN_SCHEMA_CONNECTION') !== false ? \getenv('TVN_SCHEMA_CONNECTION') : 'default',
];
