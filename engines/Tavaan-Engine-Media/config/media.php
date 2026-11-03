<?php

declare(strict_types=1);

return [
    /*| حداکثر اندازه فایل (bytes) */
    'max_size' => (int) (\getenv('TVN_MEDIA_MAX_SIZE') !== false ? \getenv('TVN_MEDIA_MAX_SIZE') : 104857600),

    /*| Extensionهای مجاز (خالی = همه از جدول پیش‌فرض) */
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'mp4', 'mp3'],

    /*| درایور پردازش تصویر: auto | gd | imagick | none */
    'image_driver' => \getenv('TVN_MEDIA_DRIVER') !== false ? \getenv('TVN_MEDIA_DRIVER') : 'auto',

    /*| تنظیمات thumbnail */
    'thumbnails' => [
        'sizes' => [
            'small'  => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 600, 'height' => 400],
            'large'  => ['width' => 1200, 'height' => 800],
        ],
    ],

    /*| WebP conversion */
    'webp' => [
        'enabled' => false,
        'quality' => 85,
    ],
];
