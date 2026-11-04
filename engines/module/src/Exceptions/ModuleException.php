<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

final class ModuleException extends TavaanException
{
    public static function manifestUnreadable(string $path): self
    {
        return new self("Cannot read module manifest: {$path}");
    }

    public static function invalidJson(string $path, string $error): self
    {
        return new self("Invalid JSON in manifest {$path}: {$error}");
    }

    public static function missingField(string $path, string $field): self
    {
        return new self("Missing required field '{$field}' in manifest {$path}");
    }

    public static function invalidId(string $id): self
    {
        return new self("Invalid module id '{$id}' (must match ^[a-z0-9\\-]+\\.[a-z0-9\\-]+$)");
    }

    public static function invalidVersion(): self
    {
        return new self('Module version must be semver (e.g. 1.0.0)');
    }

    public static function notFound(string $id): self
    {
        return new self("Module '{$id}' not found.");
    }

    public static function circularDependency(string $id): self
    {
        return new self("Circular dependency detected for module '{$id}'.");
    }
}
