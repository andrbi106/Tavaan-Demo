<?php
declare(strict_types=1);
return [
    'driver' => \getenv('TVN_MAIL_DRIVER') !== false ? \getenv('TVN_MAIL_DRIVER') : 'log',
    'log_path' => \getenv('TVN_MAIL_LOG') !== false ? \getenv('TVN_MAIL_LOG') : 'storage/logs/mail.log',
    'from' => [
        'address' => \getenv('TVN_MAIL_FROM') !== false ? \getenv('TVN_MAIL_FROM') : 'noreply@example.com',
        'name' => \getenv('TVN_MAIL_FROM_NAME') !== false ? \getenv('TVN_MAIL_FROM_NAME') : 'Tavaan',
    ],
    'smtp' => [
        'host' => \getenv('TVN_SMTP_HOST') !== false ? \getenv('TVN_SMTP_HOST') : '127.0.0.1',
        'port' => (int) (\getenv('TVN_SMTP_PORT') !== false ? \getenv('TVN_SMTP_PORT') : 587),
        'encryption' => \getenv('TVN_SMTP_ENCRYPTION') !== false ? \getenv('TVN_SMTP_ENCRYPTION') : 'tls',
        'username' => \getenv('TVN_SMTP_USERNAME') !== false ? \getenv('TVN_SMTP_USERNAME') : null,
        'password' => \getenv('TVN_SMTP_PASSWORD') !== false ? \getenv('TVN_SMTP_PASSWORD') : null,
    ],
];
