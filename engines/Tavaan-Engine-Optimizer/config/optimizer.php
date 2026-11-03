<?php
declare(strict_types=1);
return [
    'cache_dir' => \getenv('TVN_OPTIMIZER_DIR') !== false ? \getenv('TVN_OPTIMIZER_DIR') : 'storage/framework/cache',
];
