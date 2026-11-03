<?php

declare(strict_types=1);

/*|--------------------------------------------------------------------------*/
/*| Compat Engine Configuration                                              */
/*|--------------------------------------------------------------------------*/
/*| پیکربندی موتور Compat — سازگاری و Polyfill.                              */
/*|                                                                         */
/*| @see https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/compat.md
/*|--------------------------------------------------------------------------*/

return [
    /*| اجبار به استفاده از Fallbackها (برای تست fallbackها) */
    'force_fallbacks' => \getenv('TVN_COMPAT_FORCE_FALLBACK') !== false
        ? \filter_var(\getenv('TVN_COMPAT_FORCE_FALLBACK'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| تنظیم درایورها (auto = تشخیص خودکار از روی افزونه‌های موجود) */
    'drivers' => [
        'math'    => \getenv('TVN_MATH_DRIVER') !== false ? \getenv('TVN_MATH_DRIVER') : 'auto',     // auto|bcmath|gmp|decimal
        'image'   => \getenv('TVN_IMAGE_DRIVER') !== false ? \getenv('TVN_IMAGE_DRIVER') : 'auto',   // auto|imagick|gd|none
        'zip'     => \getenv('TVN_ZIP_DRIVER') !== false ? \getenv('TVN_ZIP_DRIVER') : 'auto',       // auto|zip|pure
        'http'    => \getenv('TVN_HTTP_DRIVER') !== false ? \getenv('TVN_HTTP_DRIVER') : 'auto',     // auto|curl|stream
        'unicode' => \getenv('TVN_UNICODE_DRIVER') !== false ? \getenv('TVN_UNICODE_DRIVER') : 'auto', // auto|intl|mbstring|native
        'mime'    => \getenv('TVN_MIME_DRIVER') !== false ? \getenv('TVN_MIME_DRIVER') : 'auto',     // auto|fileinfo|magic
    ],

    /*| هشدار دادن در زمان استفاده از fallback */
    'warn_on_fallback' => \getenv('TVN_COMPAT_WARN') !== false
        ? \filter_var(\getenv('TVN_COMPAT_WARN'), \FILTER_VALIDATE_BOOL)
        : true,
];
