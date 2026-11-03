<?php
declare(strict_types=1);
return [
    'key' => \getenv('TVN_AUDIT_KEY') !== false ? \getenv('TVN_AUDIT_KEY') : '',
    'table' => 'tvn_audit_logs',
    'hash_algorithm' => 'sha256',
];
