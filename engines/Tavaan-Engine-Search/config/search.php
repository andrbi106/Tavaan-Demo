<?php

declare(strict_types=1);

return [
    /*| درایور پیش‌فرض: native | mysql | pgsql | sqlite | meilisearch | typesense */
    'driver' => \getenv('TVN_SEARCH_DRIVER') !== false ? \getenv('TVN_SEARCH_DRIVER') : 'native',

    /*| تعداد نتایج پیش‌فرض */
    'per_page' => (int) (\getenv('TVN_SEARCH_PER_PAGE') !== false ? \getenv('TVN_SEARCH_PER_PAGE') : 20),

    /*| تنظیمات highlight */
    'highlight' => [
        'pre_tag' => '<mark>',
        'post_tag' => '</mark>',
    ],

    /*| تنظیمات درایور mysql (در صورت استفاده) */
    'mysql' => [
        'connection' => 'default',
        'min_word_length' => 3,
    ],
];
