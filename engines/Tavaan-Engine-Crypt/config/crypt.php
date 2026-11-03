<?php

declare(strict_types=1);

/*|--------------------------------------------------------------------------*/
/*| Crypt Engine Configuration                                                */
/*|--------------------------------------------------------------------------*/
/*| پیکربندی موتور Crypt.                                                    */
/*|                                                                         */
/*| @see https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/crypt.md
/*|--------------------------------------------------------------------------*/

return [
    /*| کلید اصلی (32 bytes، base64 encoded برای ذخیره‌سازی امن) */
    'key' => \getenv('TVN_CRYPT_KEY') !== false
        ? \base64_decode(\getenv('TVN_CRYPT_KEY'), true) ?: ''
        : '',

    /*| نسخهٔ کلید فعلی */
    'key_version' => (int) (\getenv('TVN_CRYPT_KEY_VERSION') !== false
        ? \getenv('TVN_CRYPT_KEY_VERSION')
        : 1),

    /*| cipher پیش‌فرض (AES-256-GCM) */
    'cipher' => \getenv('TVN_CRYPT_CIPHER') !== false ? \getenv('TVN_CRYPT_CIPHER') : 'aes-256-gcm',

    /*| کلیدهای قدیمی برای رمزگشایی داده‌های قدیمی */
    'previous_keys' => [],
];
