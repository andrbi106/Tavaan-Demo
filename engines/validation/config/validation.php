<?php

declare(strict_types=1);

return [
    /*| حالت strict (reject unknown fields) */
    'strict' => \getenv('TVN_VALIDATION_STRICT') !== false
        ? \filter_var(\getenv('TVN_VALIDATION_STRICT'), \FILTER_VALIDATE_BOOL)
        : false,

    /*| stop on first error */
    'stop_on_first' => false,
];
