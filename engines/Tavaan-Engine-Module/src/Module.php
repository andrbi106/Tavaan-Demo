<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

/**
 * Module — یک ماژول کشف‌شده با manifest.
 */
final class Module
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $version,
        public readonly string $path,
        public readonly array $manifest,
        public bool $enabled = true,
    ) {
    }

    public function providers(): array
    {
        return $this->manifest['providers'] ?? [];
    }

    public function autoload(): array
    {
        return $this->manifest['autoload'] ?? [];
    }

    /**
     * @return list<string>
     */
    public function requires(): array
    {
        return $this->manifest['requires'] ?? [];
    }

    /**
     * @return list<string>
     */
    public function softRequires(): array
    {
        return $this->manifest['soft_requires'] ?? [];
    }

    public static function fromArray(array $data, string $path): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'] ?? $data['id'],
            version: $data['version'],
            path: $path,
            manifest: $data,
            enabled: $data['enabled'] ?? true,
        );
    }
}
