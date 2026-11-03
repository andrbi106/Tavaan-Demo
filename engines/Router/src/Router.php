<?php
declare(strict_types=1);
namespace Tavaan\Engine\Http\Routing;

final class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly mixed $action,
        public readonly string $name = '',
        public array $middleware = [],
        public ?array $where = null,
    ) {}

    public function name(string $name): self
    {
        return new self($this->method, $this->uri, $this->action, $name, $this->middleware, $this->where);
    }

    public function middleware(array|string $mw): self
    {
        return new self($this->method, $this->uri, $this->action, $this->name, array_merge($this->middleware, (array)$mw), $this->where);
    }

    public function where(string $param, string $pattern): self
    {
        $where = ($this->where ?? []) + [$param => $pattern];
        return new self($this->method, $this->uri, $this->action, $this->name, $this->middleware, $where);
    }
}

final class RouteCollection
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function add(Route $route): void
    {
        $this->routes[] = $route;
        if ($route->name !== '') $this->namedRoutes[$route->name] = $route;
    }

    public function match(string $method, string $uri): ?Route
    {
        foreach ($this->routes as $route) {
            if ($this->matches($route, $method, $uri)) return $route;
        }
        return null;
    }

    private function matches(Route $route, string $method, string $uri): bool
    {
        if (strcasecmp($route->method, $method) !== 0 && $route->method !== 'ANY') return false;
        $pattern = $this->compilePattern($route);
        return (bool) preg_match($pattern, $uri);
    }

    private function compilePattern(Route $route): string
    {
        $pattern = $route->uri;
        // Replace {param} with named capture groups
        $pattern = preg_replace_callback('/\{(\w+)(\?)?\}/', function($m) use ($route) {
            $name = $m[1];
            $optional = $m[2] ?? '';
            $constraint = $route->where[$name] ?? '[^/]+';
            return "(?P<{$name}>{$constraint})" . ($optional ? '?' : '');
        }, $pattern);
        return '#^' . $pattern . '$#u';
    }

    public function getRoute(string $name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function all(): array { return $this->routes; }
}

final class UrlGenerator
{
    public function __construct(private readonly RouteCollection $routes) {}

    public function route(string $name, array $params = []): ?string
    {
        $route = $this->routes->getRoute($name);
        if ($route === null) return null;
        $uri = $route->uri;
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', (string)$value, $uri);
            $uri = str_replace('{' . $key . '?}', (string)$value, $uri);
        }
        return $uri;
    }
}

final class Router
{
    private RouteCollection $routes;
    private ?UrlGenerator $generator = null;
    private array $groupStack = [];

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, mixed $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function any(string $uri, mixed $action): Route
    {
        return $this->addRoute('ANY', $uri, $action);
    }

    public function match(array $methods, string $uri, mixed $action): Route
    {
        return $this->addRoute(implode('|', $methods), $uri, $action);
    }

    public function resource(string $name, string $controller): void
    {
        $this->get("/{$name}", [$controller, 'index'])->name("{$name}.index");
        $this->get("/{$name}/create", [$controller, 'create'])->name("{$name}.create");
        $this->post("/{$name}", [$controller, 'store'])->name("{$name}.store");
        $this->get("/{$name}/{{$name}}", [$controller, 'show'])->name("{$name}.show");
        $this->get("/{$name}/{{$name}}/edit", [$controller, 'edit'])->name("{$name}.edit");
        $this->put("/{$name}/{{$name}}", [$controller, 'update'])->name("{$name}.update");
        $this->delete("/{$name}/{{$name}}", [$controller, 'destroy'])->name("{$name}.destroy");
    }

    public function apiResource(string $name, string $controller): void
    {
        $this->get("/{$name}", [$controller, 'index'])->name("{$name}.index");
        $this->post("/{$name}", [$controller, 'store'])->name("{$name}.store");
        $this->get("/{$name}/{{$name}}", [$controller, 'show'])->name("{$name}.show");
        $this->put("/{$name}/{{$name}}", [$controller, 'update'])->name("{$name}.update");
        $this->delete("/{$name}/{{$name}}", [$controller, 'destroy'])->name("{$name}.destroy");
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $route = $this->routes->match($method, $uri);
        if ($route === null) return null;
        // Extract params
        $params = [];
        $pattern = preg_replace_callback('/\{(\w+)(\?)?\}/', function($m) use (&$params, $route) {
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $route->uri);
        if (preg_match('#^' . $pattern . '$#u', $uri, $matches)) {
            foreach ($matches as $k => $v) {
                if (!is_int($k)) $params[$k] = $v;
            }
        }
        return ['route' => $route, 'params' => $params];
    }

    public function getRoutes(): RouteCollection { return $this->routes; }

    public function url(string $name, array $params = []): ?string
    {
        if ($this->generator === null) $this->generator = new UrlGenerator($this->routes);
        return $this->generator->route($name, $params);
    }

    private function extractParams(Route $route, string $uri): array
    {
        $params = [];
        $pattern = preg_replace_callback('/\{(\w+)(\?)?\}/', function($m) use (&$params) {
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $route->uri);
        if (preg_match('#^' . $pattern . '$#u', $uri, $matches)) {
            foreach ($matches as $k => $v) {
                if (!is_int($k)) $params[$k] = $v;
            }
        }
        return $params;
    }

    private function addRoute(string $method, string $uri, mixed $action): Route
    {
        // Apply group prefix
        $prefix = '';
        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) $prefix .= '/' . trim($group['prefix'], '/');
            if (isset($group['middleware'])) $middleware = array_merge($middleware, (array)$group['middleware']);
        }
        $fullUri = $prefix ? $prefix . $uri : $uri;
        $route = new Route($method, $fullUri, $action, middleware: $middleware);
        $this->routes->add($route);
        return $route;
    }
}


final class RouterServiceProvider
{
    private ?Router $router = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->router = new Router(); }
    public function boot(): void {}
    public function provides(): array { return [Router::class, Route::class]; }
    public function getRouter(): Router { if ($this->router === null) $this->register(); return $this->router; }

    /**
     * action — build a URL for a controller@method action.
     */
    public function action(string $action, array $params = []): string
    {
        // Convert "Controller@index" to "/controller/index"
        [$controller, $method] = explode('@', $action, 2) + ['', 'index'];
        $path = '/' . strtolower(str_replace('Controller', '', $controller));
        if ($method !== '' && $method !== 'index') {
            $path .= '/' . $method;
        }
        foreach ($params as $k => $v) {
            $path .= '/' . (string) $v;
        }
        return $path;
    }
    /**
     * asset — build a URL for a static asset.
     */
    public function asset(string $path, ?bool $secure = null): string
    {
        $scheme = $secure === null ? '' : ($secure ? 'https' : 'http');
        $prefix = $scheme !== '' ? $scheme . '://' : '';
        return $prefix . '/' . ltrim($path, '/');
    }
    /**
     * current — return the current request URI.
     */
    public function current(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
    /**
     * full — return the full URL with query string.
     */
    public function full(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        if ($qs !== '' && !str_contains($uri, '?')) {
            $uri .= '?' . $qs;
        }
        return $uri;
    }
    /**
     * isValidSignature
     */
    public function isValidSignature(string $url): bool
    {
        $parts = parse_url($url);
        if (!isset($parts['query'])) { return false; }
        parse_str($parts['query'], $params);
        $signature = $params['signature'] ?? '';
        if ($signature === '') { return false; }
        unset($params['signature']);
        $path = $parts['path'] ?? '/';
        $expected = $this->generateSignature($path, $params);
        return hash_equals($expected, $signature);
    }
    /**
     * previous — return the referer or fallback URL.
     */
    public function previous(?string $fallback = null): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $fallback ?? '/';
    }
    /**
     * signed — build a signed URL with optional expiry.
     */
    public function signed(string $path, ?int $expiration = null): string
    {
        $params = [];
        if ($expiration !== null) {
            $params['expires'] = $expiration;
        }
        $signature = $this->generateSignature($path, $params);
        $params['signature'] = $signature;
        $query = http_build_query($params);
        return $path . (str_contains($path, '?') ? '&' : '?') . $query;
    }
    /**
     * temporarySignedRoute — build a signed named route URL with expiry.
     */
    public function temporarySignedRoute(string $name, int $expiration, array $params = []): string
    {
        $path = $this->getRouter()->url($name, $params) ?? $name;
        $params['expires'] = $expiration;
        $signature = $this->generateSignature($path, $params);
        $params['signature'] = $signature;
        return $path . '?' . http_build_query($params);
    }
    /**
     * to — build a URL for a path with query params.
     */
    public function to(string $path, array $params = [], ?bool $secure = null): string
    {
        $scheme = $secure === null ? '' : ($secure ? 'https' : 'http');
        $prefix = $scheme !== '' ? $scheme . '://' : '';
        if ($params !== []) {
            $path .= (str_contains($path, '?') ? '&' : '?') . http_build_query($params);
        }
        return $prefix . '/' . ltrim($path, '/');
    }

    /**
     * bind
     */
    public function bind(string $key, mixed $binder): self
    {
        return $this;
    }
    /**
     * currentRouteName — name of the matched route (or null).
     */
    public function currentRouteName(): ?string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $dispatch = $this->getRouter()->dispatch($method, $uri);
        if ($dispatch === null) { return null; }
        return $dispatch['route']->name ?: null;
    }
    /**
     * domain
     */
    public function domain(string $domain): self
    {
        return $this;
    }
    /**
     * fallback
     */
    public function fallback(callable $callback): self
    {
        return $this;
    }
    /**
     * has
     */
    public function has(string $key): bool
    {
        return $this->routes->getRoute($key) !== null;
    }
    /**
     * head — register a HEAD route.
     */
    public function head(string $uri, mixed $action = null): ?Route
    {
        if ($action === null) { return null; }
        $router = $this->getRouter();
        if (method_exists($router, 'addRoute')) {
            return $router->addRoute('HEAD', $uri, $action);
        }
        // fall back to adding a route via get() (GET/HEAD equivalent in some routers)
        return $router->get($uri, $action);
    }
    /** @var array<string, callable> */
    private array $bindings = [];

    /**
     * model — bind a route param to a model resolver.
     */
    public function model(string $key, callable $resolver): self
    {
        $this->bindings[$key] = $resolver;
        return $this;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }
    /**
     * options — register an OPTIONS route.
     */
    public function options(string $uri, mixed $action = null): ?Route
    {
        if ($action === null) { return null; }
        $router = $this->getRouter();
        return method_exists($router, 'addRoute') ? $router->addRoute('OPTIONS', $uri, $action) : $router->any($uri, $action);
    }
    /**
     * permanentRedirect — register a 301 redirect route.
     */
    public function permanentRedirect(string $from, string $to): self
    {
        $this->getRouter()->get($from, function () use ($to) {
            header('Location: ' . $to, true, 301);
            exit;
        });
        return $this;
    }
    /**
     * prefix
     */
    public function prefix(string $prefix): self
    {
        return $this;
    }
    /**
     * redirect
     */
    public function redirect(string $to, int $status = 302): self
    {
        return $this;
    }
    /**
     * view — register a route that just renders a view template.
     */
    public function view(string $uri, string $template, array $data = []): Route
    {
        return $this->getRouter()->get($uri, function () use ($template, $data) {
            // Delegate to the View engine when present; basic echo for now.
            $out = '<!-- view: ' . htmlspecialchars($template) . ' -->';
            foreach ($data as $k => $v) {
                $out .= '<!-- ' . htmlspecialchars($k) . ' -->';
            }
            return $out;
        });
    }
}


