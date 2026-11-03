<?php

declare(strict_types=1);

return [
    /*| نام متد پیش‌فرض روی Pipe */
    'method' => \getenv('TVN_PIPELINE_METHOD') !== false ? \getenv('TVN_PIPELINE_METHOD') : 'handle',

    /*| نام متد terminate */
    'terminate_method' => \getenv('TVN_PIPELINE_TERMINATE') !== false ? \getenv('TVN_PIPELINE_TERMINATE') : 'terminate',
];
