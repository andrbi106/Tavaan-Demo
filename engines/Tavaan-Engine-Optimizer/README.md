# Optimizer

> بهینه‌سازی و کش زمان اجرا: Config, Routes, Views, Autoload, Events.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.optimizer` |
| DOC-ID | `DOC-037` |
| Namespace | `Tavaan\Engine\Optimizer` |
| اولویت | P1 (فاز M1) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Optimizer |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/optimizer.md

## استفاده

```php
use Tavaan\Engine\Optimizer\Optimizer;

$opt = new Optimizer([
    'cache_dir' => 'storage/framework/cache',
    'config_data' => $configArray,
    'routes_data' => $routesArray,
    'classmap_data' => $classMap,
]);

// کش همه‌ی cacherها
$result = $opt->optimize();
echo $result->summary();
// config: 1.50 ms
// routes: 0.30 ms
// views: 0.10 ms
// autoload: 5.20 ms
// events: 0.20 ms
// Total: 7.30 ms

// بررسی وضعیت
$opt->isCached('config');  // true
$opt->isCached('routes');  // true

// پاک کردن همه
$opt->clear();

// پاک کردن یکی
$opt->clearOne('config');
```

## مستندات

[docs/03-engines/optimizer.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/optimizer.md)

---
*Generated on 2026-11-03.*
