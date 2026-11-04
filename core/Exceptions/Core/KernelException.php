<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions\Core;

use Tavaan\Core\Exceptions\TavaanException;

class KernelException extends TavaanException
{
    public static function forAlreadyBooted(): self
    {
        return new self('core.kernel.already_booted', [], 'Kernel قبلاً boot شده است.');
    }
    public static function forNotBooted(): self
    {
        return new self('core.kernel.not_booted', [], 'Kernel boot نشده است.');
    }
}
