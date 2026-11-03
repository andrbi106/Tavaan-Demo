<?php
declare(strict_types=1);
namespace Tavaan\Engine\Api;

final class ApiResource
{
    public function __construct(
        public readonly mixed $resource,
        public readonly ?string $wrap = 'data',
    ) {}

    public function toArray(): array
    {
        $data = is_array($this->resource) ? $this->resource : (is_object($this->resource) && method_exists($this->resource, 'toArray') ? $this->resource->toArray() : []);
        return $this->wrap ? [$this->wrap => $data] : $data;
    }
}

final class ApiCollection
{
    public function __construct(
        public readonly array $resources,
        public readonly ?string $wrap = 'data',
        public readonly ?array $meta = null,
    ) {}

    public function toArray(): array
    {
        $data = array_map(fn($r) => is_array($r) ? $r : (is_object($r) && method_exists($r, 'toArray') ? $r->toArray() : []), $this->resources);
        $result = [$this->wrap => $data];
        if ($this->meta) $result['meta'] = $this->meta;
        return $result;
    }
}

final class JsonApiResponse
{
    public static function ok(mixed $data, array $meta = []): array
    {
        $result = ['data' => $data];
        if (!empty($meta)) $result['meta'] = $meta;
        return $result;
    }

    public static function error(string $message, int $status = 400, string $code = '', array $errors = []): array
    {
        $result = ['error' => ['message' => $message, 'status' => $status]];
        if ($code !== '') $result['error']['code'] = $code;
        if (!empty($errors)) $result['error']['errors'] = $errors;
        return $result;
    }

    public static function paginated(array $items, int $total, int $perPage, int $currentPage): array
    {
        return [
            'data' => $items,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => (int) ceil($total / max(1, $perPage)),
                'from' => $total > 0 ? ($currentPage - 1) * $perPage + 1 : null,
                'to' => min($currentPage * $perPage, $total),
            ],
        ];
    }
}

final class ApiServiceProvider
{
    public function __construct(private readonly array $config = []) {}
    public function register(): void {}
    public function boot(): void {}
    public function provides(): array { return [ApiResource::class, ApiCollection::class, JsonApiResponse::class]; }
}
