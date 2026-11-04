<?php
declare(strict_types=1);
namespace Tavaan\Core\Container;

/** طول عمر binding در کانتینر. */
enum Lifetime: string
{
    case Transient = 'transient';  // هر بار نمونهٔ جدید
    case Singleton = 'singleton';  // فقط یک نمونه (shared)
}
