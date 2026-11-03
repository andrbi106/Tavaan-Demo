<?php

declare(strict_types=1);

return [
    /*| زبان پیش‌فرض */
    'locale' => \getenv('TVN_LOCALE') !== false ? \getenv('TVN_LOCALE') : 'fa',

    /*| زبان fallback */
    'fallback' => \getenv('TVN_FALLBACK_LOCALE') !== false ? \getenv('TVN_FALLBACK_LOCALE') : 'en',

    /*| مسیر فایل‌های ترجمه */
    'lang_dir' => \getenv('TVN_LANG_DIR') !== false ? \getenv('TVN_LANG_DIR') : 'lang',
];
