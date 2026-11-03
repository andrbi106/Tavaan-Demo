<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * RefCodeGenerator — تولید کد ارجاع یکتا برای خطاها (برای ردیابی پشتیبانی).
 */
final class RefCodeGenerator
{
    /**
     * تولید کد ارجاع کوتاه (8 کاراکتر).
     */
    public static function generate(): string
    {
        $bytes = \random_bytes(4);
        return \strtoupper(\bin2hex($bytes));
    }
}
