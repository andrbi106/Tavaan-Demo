<?php
declare(strict_types=1);
return [
    'path' => \getenv('TVN_VIEW_PATH') !== false ? \getenv('TVN_VIEW_PATH') : 'views',
    'compiled' => \getenv('TVN_VIEW_COMPILED') !== false ? \getenv('TVN_VIEW_COMPILED') : 'storage/framework/views',
    'extension' => 'tv.php',
];
