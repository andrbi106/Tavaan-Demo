<?php

declare(strict_types=1);

return [
    /*| حالت debug */
    'debug' => \getenv('TVN_DEBUG') !== false
        ? \filter_var(\getenv('TVN_DEBUG'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| ثبت خودکار handlerها */
    'auto_register' => \getenv('TVN_ERROR_AUTO_REGISTER') !== false
        ? \filter_var(\getenv('TVN_ERROR_AUTO_REGISTER'), \FILTER_VALIDATE_BOOL)
        : true,

    /*| تبدیل E_DEPRECATED به خطا */
    'strict_deprecated' => \getenv('TVN_ERROR_STRICT') !== false
        ? \filter_var(\getenv('TVN_ERROR_STRICT'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| نمایش stack trace در debug */
    'show_trace' => true,

    /*| کلیدهای حساس برای mask */
    'sensitive_keys' => ['password', 'token', 'api_key', 'secret', 'cvv', 'pin'],
];
