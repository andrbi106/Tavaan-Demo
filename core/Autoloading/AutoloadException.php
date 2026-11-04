<?php
declare(strict_types=1);
namespace Tavaan\Core\Autoloading;

use Tavaan\Core\Exceptions\TavaanException;

/** خطای autoloader — نام کلاس ناامن، path traversal، کلاس پیدا نشد. */
final class AutoloadException extends TavaanException
{
    public static function forUnsafeClassName(string $class): self
    {
        return new self(
            'autoload.unsafe_class',
            ['class' => $class],
            "نام کلاس '{$class}' ناامن است (حاوی کاراکترهای غیرمجاز)."
        );
    }

    public static function forPathTraversal(string $class, string $path): self
    {
        return new self(
            'autoload.path_traversal',
            ['class' => $class, 'path' => $path],
            "Path traversal تشخیص داده شد برای کلاس '{$class}'."
        );
    }
}
