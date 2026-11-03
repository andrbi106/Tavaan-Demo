<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

use Tavaan\Engine\Module\Exceptions\ModuleException;

/**
 * ManifestParser — پارس module.json با Schema validation.
 */
final class ManifestParser
{
    public function parse(string $path): Module
    {
        if (!\file_exists($path)) {
            throw ModuleException::manifestUnreadable($path);
        }
        $raw = \file_get_contents($path);
        if ($raw === false) {
            throw ModuleException::manifestUnreadable($path);
        }
        try {
            $data = \json_decode($raw, true, 32, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ModuleException::invalidJson($path, $e->getMessage());
        }
        $this->validateSchema($data, $path);
        return Module::fromArray($data, \dirname($path));
    }

    private function validateSchema(array $data, string $path): void
    {
        $required = ['id', 'name', 'version', 'autoload', 'providers'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw ModuleException::missingField($path, $field);
            }
        }
        if (!\preg_match('/^[a-z0-9\-]+\.[a-z0-9\-]+$/', $data['id'])) {
            throw ModuleException::invalidId($data['id']);
        }
        if (!$this->isSemver($data['version'])) {
            throw ModuleException::invalidVersion();
        }
    }

    private function isSemver(string $v): bool
    {
        return (bool) \preg_match('/^\d+\.\d+\.\d+(-[a-z0-9\.\-]+)?(\+[a-z0-9\.\-]+)?$/i', $v);
    }
}
