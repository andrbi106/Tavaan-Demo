<?php

declare(strict_types=1);

namespace Tavaan\Engine\Api;

/**
 * JsonResource — wraps a model into JSON-serialisable form.
 */
class JsonResource
{
    public function __construct(
        public mixed $resource = null,
        protected array $additional = [],
    ) {}

    /**
     * make — instantiate a resource for a single model.
     */
    public static function make(mixed $resource): static
    {
        return new static($resource);
    }

    /**
     * collection — wrap a list of models in resource instances.
     * @param iterable<mixed> $resources
     * @return list<static>
     */
    public static function collection(mixed $resources): array
    {
        $list = is_array($resources) ? $resources : iterator_to_array($resources);
        return array_map(fn($r) => new static($r), $list);
    }

    /**
     * toArray — default serialization; override in subclasses.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if (is_array($this->resource)) {
            return $this->resource;
        }
        if (is_object($this->resource) && method_exists($this->resource, 'toArray')) {
            return $this->resource->toArray();
        }
        if (is_object($this->resource)) {
            return get_object_vars($this->resource);
        }
        return ['data' => $this->resource];
    }

    /**
     * when — include value only when condition is truthy.
     * @return array<string, mixed>|mixed
     */
    public function when(bool $condition, mixed $value, mixed $default = null): mixed
    {
        $resolved = is_callable($value) && !is_string($value) ? $value() : $value;
        if ($condition) { return $resolved; }
        return is_callable($default) && !is_string($default) ? $default() : $default;
    }

    /**
     * whenLoaded — include attribute only if relation is loaded.
     * @param string $relation
     * @param mixed $value
     * @param mixed $default
     */
    public function whenLoaded(string $relation, mixed $value = null, mixed $default = null): mixed
    {
        $resource = $this->resource;
        if (!is_object($resource)) { return $default; }
        // convention: relationLoaded($relation) method exists
        if (method_exists($resource, 'relationLoaded') && $resource->relationLoaded($relation)) {
            return $value !== null ? (is_callable($value) && !is_string($value) ? $value() : $value) : null;
        }
        return $default;
    }

    /**
     * whenCounted — include a counted relation when count is available.
     */
    public function whenCounted(string $relation, ?int $count = null, mixed $default = null): mixed
    {
        $resource = $this->resource;
        if (!is_object($resource)) { return $default; }
        if (method_exists($resource, 'relationCount') || $count !== null) {
            return $count ?? $resource->{$relation . '_count'} ?? 0;
        }
        return $default;
    }

    /**
     * mergeWhen — merge a callable-derived array when condition holds.
     * @return array<string, mixed>
     */
    public function mergeWhen(bool $condition, mixed $array, mixed $default = []): array
    {
        if (!$condition) { return is_array($default) ? $default : (is_callable($default) ? $default() : []); }
        return is_callable($array) ? $array() : (array) $array;
    }

    /**
     * response — emit a JSON response (سبکِ تَوان).
     */
    public function response(int $status = 200, array $headers = []): array
    {
        return [
            'status' => $status,
            'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
            'body' => json_encode($this->toArray() + $this->additional, JSON_UNESCAPED_UNICODE),
        ];
    }
}
