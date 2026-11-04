<?php

declare(strict_types=1);

namespace Tavaan\Engine\Scaffold;

/**
 * GeneratorManager — registry + dispatcher for code generators.
 */
final class GeneratorManager
{
    /** @var array<string, callable> */
    private array $generators = [];

    /**
     * generate — invoke a registered generator by name with options.
     * @param array<string, mixed> $options
     * @return array{files: list<string>, generator: string}|null
     */
    public function generate(string $name, array $options = []): ?array
    {
        if (!isset($this->generators[$name])) {
            return null;
        }
        $files = ($this->generators[$name])($options);
        return [
            'generator' => $name,
            'files' => is_array($files) ? $files : [$files],
        ];
    }

    /**
     * register — add a generator by name.
     * @param callable(array<string, mixed>): list<string>|string $callback
     */
    public function register(string $name, ?callable $callback = null): self
    {
        if ($callback !== null) {
            $this->generators[$name] = $callback;
        }
        return $this;
    }

    /**
     * has — check whether a generator is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->generators[$name]);
    }

    /**
     * all — return list of registered generator names.
     * @return list<string>
     */
    public function all(): array
    {
        return array_keys($this->generators);
    }
}
