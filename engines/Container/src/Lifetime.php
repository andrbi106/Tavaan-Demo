<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container;

/**
 * Lifetime یک binding را توصیف می‌کند.
 */
enum Lifetime: string
{
    /** یک نمونه برای کل فرایند. */
    case Singleton = 'singleton';

    /** یک نمونه به ازای هر Scope. */
    case Scoped = 'scoped';

    /** هر بار make = نمونهٔ جدید. */
    case Transient = 'transient';

    /** فراخوانی Factory closure در هر make. */
    case Factory = 'factory';

    /** نمونه از پیش ساخته‌شده. */
    case Instance = 'instance';
}
