<?php

declare(strict_types=1);

return [
    /*| تقویم پیش‌فرض: jalali | gregorian | islamic */
    'default' => \getenv('TVN_CALENDAR') !== false ? \getenv('TVN_CALENDAR') : 'jalali',

    /*| اولین روز هفته: 0 = شنبه، 1 = یکشنبه، ... */
    'first_day_of_week' => 0,

    /*| نمایش نام روز در تقویم */
    'show_weekdays' => true,

    /*| تبدیل اعداد به فارسی */
    'persian_digits' => \getenv('TVN_PERSIAN_DIGITS') !== false
        ? \filter_var(\getenv('TVN_PERSIAN_DIGITS'), \FILTER_VALIDATE_BOOL)
        : true,
];
