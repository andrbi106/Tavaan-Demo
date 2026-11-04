<?php

declare(strict_types=1);

namespace Tavaan\Engine\Api\OpenApi;

/**
 * OpenApiGenerator — emits OpenAPI 3.0 specs from route metadata.
 */
final class OpenApiGenerator
{
    /** @var array<string, mixed> */
    private array $spec = [];

    public function __construct(string $title = 'Tavaan API', string $version = '1.0.0')
    {
        $this->spec = [
            'openapi' => '3.0.3',
            'info' => ['title' => $title, 'version' => $version],
            'paths' => [],
            'components' => ['schemas' => new \stdClass()],
        ];
    }

    /**
     * generate — register a route/operation in the spec.
     * @param array<string, mixed> $operation
     */
    public function generate(string $method, string $path, array $operation = []): self
    {
        $method = strtolower($method);
        if (!isset($this->spec['paths'][$path])) {
            $this->spec['paths'][$path] = new \stdClass();
        }
        $this->spec['paths'][$path] = array_merge(
            (array) $this->spec['paths'][$path],
            [$method => array_merge(['responses' => ['200' => ['description' => 'OK']]], $operation)]
        );
        return $this;
    }

    public function toJson(): string
    {
        return json_encode($this->spec, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function toYaml(): string
    {
        // Minimal hand-rolled YAML emitter (no external dep).
        return $this->toYamlImpl($this->spec, 0);
    }

    public function toArray(): array
    {
        return $this->spec;
    }

    private function toYamlImpl(mixed $data, int $indent): string
    {
        $out = '';
        $pad = str_repeat('  ', $indent);
        if (is_array($data)) {
            $isList = array_is_list($data);
            foreach ($data as $k => $v) {
                if (is_array($v) || $v instanceof \stdClass) {
                    $arr = (array) $v;
                    if ($arr === []) {
                        $out .= $pad . $k . ": []\n";
                    } else {
                        $out .= $pad . $k . ":\n" . $this->toYamlImpl($arr, $indent + 1);
                    }
                } elseif (is_string($v)) {
                    $prefix = $isList ? '- ' : $k . ': ';
                    $out .= $pad . $prefix . '"' . addslashes($v) . "\"\n";
                } elseif (is_bool($v)) {
                    $prefix = $isList ? '- ' : $k . ': ';
                    $out .= $pad . $prefix . ($v ? 'true' : 'false') . "\n";
                } elseif (is_null($v)) {
                    $prefix = $isList ? '- ' : $k . ': ';
                    $out .= $pad . $prefix . "null\n";
                } else {
                    $prefix = $isList ? '- ' : $k . ': ';
                    $out .= $pad . $prefix . (string) $v . "\n";
                }
            }
        }
        return $out;
    }
}
