<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions\Core;

use Tavaan\Core\Exceptions\TavaanException;

class ContainerException extends TavaanException
{
    public static function forUnresolved(string $abstract): self
    {
        return new self('core.container.unresolved', ['abstract' => $abstract], "نمی‌توان '{$abstract}' را resolve کرد.");
    }
    public static function forNotInstantiable(string $class): self
    {
        return new self('core.container.not_instantiable', ['class' => $class], "کلاس '{$class}' قابل instantiation نیست.");
    }
}
