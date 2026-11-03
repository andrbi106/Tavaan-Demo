<?php

declare(strict_types=1);

/*|--------------------------------------------------------------------------*/
/*| Container Engine Configuration                                            */
/*|--------------------------------------------------------------------------*/
/*| پیکربندی موتور Container — ظرف تزریق وابستگی.                            */
/*|                                                                         */
/*| @see https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/container.md
/*|--------------------------------------------------------------------------*/

return [
    /*| مدیریت کش Reflection */
    'reflection_cache' => [
        'enabled' => \getenv('TVN_CONTAINER_REFL_CACHE') !== false
            ? \filter_var(\getenv('TVN_CONTAINER_REFL_CACHE'), \FILTER_VALIDATE_BOOL)
            : true,
    ],

    /*| حداکثر عمق وابستگی (برای تشخیص cycle) */
    'max_depth' => (int) (\getenv('TVN_CONTAINER_MAX_DEPTH') !== false ? \getenv('TVN_CONTAINER_MAX_DEPTH') : 50),

    /*| حالت Strict (استثنا در صورت نبود binding) */
    'strict' => \getenv('TVN_CONTAINER_STRICT') !== false
        ? \filter_var(\getenv('TVN_CONTAINER_STRICT'), \FILTER_VALIDATE_BOOL)
        : true,

    /*| تلاش برای auto-build در صورت نبود binding */
    'auto_build' => \getenv('TVN_CONTAINER_AUTO_BUILD') !== false
        ? \filter_var(\getenv('TVN_CONTAINER_AUTO_BUILD'), \FILTER_VALIDATE_BOOL)
        : true,
];
