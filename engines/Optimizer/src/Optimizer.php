<?php

declare(strict_types=1);

namespace Tavaan\Engine\Optimizer;

/**
 * قرارداد Cacher — هر کاش (config, routes, views, etc.) این را پیاده‌سازی می‌کند.
 */
interface Cacher
{
    public function cache(): bool;
    public function clear(): void;
    public function isCached(): bool;
    public function getCachedPath(): string;
}

/**
 * OptimizerResult — نتیجهٔ اجرای optimize().
 */
final class OptimizerResult
{
    /**
     * @param array<string, float> $times
     */
    public function __construct(
        public readonly array $times,
        public readonly float $totalMs,
    ) {
    }

    public function summary(): string
    {
        $lines = [];
        foreach ($this->times as $name => $ms) {
            $lines[] = \sprintf('  %s: %.2f ms', $name, $ms);
        }
        $lines[] = \sprintf('  Total: %.2f ms', $this->totalMs);
        return \implode("
", $lines);
    }
}

/**
 * FileCacher — base class برای cacherهایی که به یک فایل cache می‌نویسند.
 */
abstract class FileCacher implements Cacher
{
    public function __construct(
        protected readonly string $cachePath,
    ) {
    }

    public function getCachedPath(): string
    {
        return $this->cachePath;
    }

    public function isCached(): bool
    {
        return \file_exists($this->cachePath);
    }

    public function clear(): void
    {
        if (\file_exists($this->cachePath)) {
            @\unlink($this->cachePath);
        }
    }

    protected function writeCache(string $content): bool
    {
        $dir = \dirname($this->cachePath);
        if (!\is_dir($dir)) {
            \mkdir($dir, 0777, true);
        }
        return (bool) \file_put_contents($this->cachePath, $content);
    }
}

/**
 * ConfigCacher — کش فایل‌های پیکربندی در یک فایل PHP.
 */
final class ConfigCacher extends FileCacher
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(string $cachePath, private readonly array $config = [])
    {
        parent::__construct($cachePath);
    }

    public function cache(): bool
    {
        $content = "<?php

// Cached by Tavaan Optimizer
return " . \var_export($this->config, true) . ";
";
        return $this->writeCache($content);
    }
}

/**
 * RouteCacher — کش routeهای ثبت‌شده.
 *
 * @param array<string, mixed> $routes
 */
final class RouteCacher extends FileCacher
{
    public function __construct(string $cachePath, private readonly array $routes = [])
    {
        parent::__construct($cachePath);
    }

    public function cache(): bool
    {
        $content = "<?php

// Cached routes
return " . \var_export($this->routes, true) . ";
";
        return $this->writeCache($content);
    }
}

/**
 * ViewCacher — کش قالب‌های کامپایل‌شده.
 */
final class ViewCacher extends FileCacher
{
    /**
     * @param array<string, string> $compiledViews
     */
    public function __construct(string $cachePath, private readonly array $compiledViews = [])
    {
        parent::__construct($cachePath);
    }

    public function cache(): bool
    {
        $content = "<?php

// Compiled views
return " . \var_export($this->compiledViews, true) . ";
";
        return $this->writeCache($content);
    }
}

/**
 * AutoloadCacher — کش class map.
 *
 * @param array<string, string> $classMap
 */
final class AutoloadCacher extends FileCacher
{
    public function __construct(string $cachePath, private readonly array $classMap = [])
    {
        parent::__construct($cachePath);
    }

    public function cache(): bool
    {
        $content = "<?php

// Class map
return " . \var_export($this->classMap, true) . ";
";
        return $this->writeCache($content);
    }
}

/**
 * EventCacher — کش event/listener map.
 *
 * @param array<string, mixed> $events
 */
final class EventCacher extends FileCacher
{
    public function __construct(string $cachePath, private readonly array $events = [])
    {
        parent::__construct($cachePath);
    }

    public function cache(): bool
    {
        $content = "<?php

// Event map
return " . \var_export($this->events, true) . ";
";
        return $this->writeCache($content);
    }
}

/**
 * Optimizer — coordinator اصلی.
 *
 * @see docs/03-engines/optimizer.md (DOC-037)
 */
final class Optimizer
{
    /** @var array<string, Cacher> */
    private array $cachers = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        $cacheDir = $config['cache_dir'] ?? 'storage/framework/cache';
        $this->cachers['config'] = new ConfigCacher("{$cacheDir}/config.php", $config['config_data'] ?? []);
        $this->cachers['routes'] = new RouteCacher("{$cacheDir}/routes.php", $config['routes_data'] ?? []);
        $this->cachers['views'] = new ViewCacher("{$cacheDir}/views.php", $config['views_data'] ?? []);
        $this->cachers['autoload'] = new AutoloadCacher("{$cacheDir}/classmap.php", $config['classmap_data'] ?? []);
        $this->cachers['events'] = new EventCacher("{$cacheDir}/events.php", $config['events_data'] ?? []);
    }

    public function optimize(): OptimizerResult
    {
        $times = [];
        $totalStart = \microtime(true);
        foreach ($this->cachers as $name => $cacher) {
            $start = \microtime(true);
            $cacher->cache();
            $times[$name] = (\microtime(true) - $start) * 1000;
        }
        $total = (\microtime(true) - $totalStart) * 1000;
        return new OptimizerResult($times, $total);
    }

    public function clear(): void
    {
        foreach ($this->cachers as $cacher) {
            $cacher->clear();
        }
    }

    public function clearOne(string $name): void
    {
        if (isset($this->cachers[$name])) {
            $this->cachers[$name]->clear();
        }
    }

    public function isCached(string $name): bool
    {
        return isset($this->cachers[$name]) && $this->cachers[$name]->isCached();
    }

    public function getCacher(string $name): ?Cacher
    {
        return $this->cachers[$name] ?? null;
    }

    /** @return array<string, Cacher> */
    public function all(): array
    {
        return $this->cachers;
    }
}

/**
 * OptimizerServiceProvider.
 */
final class OptimizerServiceProvider
{
    private ?Optimizer $optimizer = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->optimizer = new Optimizer($this->config);
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [Optimizer::class];
    }

    public function getOptimizer(): Optimizer
    {
        if ($this->optimizer === null) $this->register();
        return $this->optimizer;
    }
}
