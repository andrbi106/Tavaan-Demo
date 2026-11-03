<?php

declare(strict_types=1);

/*|--------------------------------------------------------------------------*/
/*| Test Engine Configuration                                                 */
/*|--------------------------------------------------------------------------*/
/*| پیکربندی موتور Test — چارچوب تست خودنوشته.                               */
/*|                                                                         */
/*| @see https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/test.md
/*|--------------------------------------------------------------------------*/

return [
    /*| حالت اجرا: native | phpunit */
    'mode' => \getenv('TVN_TEST_MODE') !== false ? \getenv('TVN_TEST_MODE') : 'native',

    /*| نمایش verbose (details) */
    'verbose' => \getenv('TVN_TEST_VERBOSE') !== false
        ? \filter_var(\getenv('TVN_TEST_VERBOSE'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| stop on first failure */
    'stop_on_failure' => \getenv('TVN_TEST_STOP') !== false
        ? \filter_var(\getenv('TVN_TEST_STOP'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| مسیر پیش‌فرض tests */
    'paths' => [
        'unit' => 'tests/Unit',
        'feature' => 'tests/Feature',
        'integration' => 'tests/Integration',
    ],
];
