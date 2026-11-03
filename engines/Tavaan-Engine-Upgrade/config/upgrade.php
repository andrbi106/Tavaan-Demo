<?php
declare(strict_types=1);
return [
    'backup_dir' => \getenv('TVN_BACKUP_DIR') !== false ? \getenv('TVN_BACKUP_DIR') : 'storage/backups',
    'auto_backup' => true,
    'min_php_version' => '8.2.0',
    'min_disk_space_mb' => 100,
];
