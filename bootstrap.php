<?php

/**
 * تَوان | Tavaan — Demo Bootstrap
 *
 * نقطهٔ ورود اپلیکیشن نمایشی تَوان.
 * این فایل core/bootstrap.php را load می‌کند و مسیرهای Demo را تنظیم می‌کند.
 *
 * ساختار:
 *   core/          → Tavaan-Core (هستهٔ فریمورک)
 *   engine-core/   → Tavaan-Engine-Core (والد موتورها)
 *   engines/       → ۵۲ موتور فرزند
 */

declare(strict_types=1);

// ── ۱) تعریف مسیر ریشه پیش از core/bootstrap.php ──────────────────────────
// این باعث می‌شود core/bootstrap.php از همین مسیر استفاده کند
if (!defined('TVN_BASE_PATH')) {
    define('TVN_BASE_PATH', dirname(__DIR__));
}

// ── ۲) بارگذاری هستهٔ فریمورک ──────────────────────────────────────────────
// core/bootstrap.php: ثابت‌ها → Autoloader → config/autoload
require __DIR__ . '/core/bootstrap.php';

// ── ۳) اصلاح مسیر Engine-Core برای ساختار Demo ───────────────────────────
// core/bootstrap.php مسیر پیش‌فرض vendor/andrbi106/tavaan-engine-core را ثبت می‌کند
// ولی در Demo، Engine-Core در engine-core/ قرار دارد
use Tavaan\Core\Autoloading\AutoloaderRegistry;

$autoloader = AutoloaderRegistry::get();

// افزودن مسیر صحیح Engine-Core
$autoloader->addNamespace('Tavaan\\EngineCore\\', 'engine-core/');

// افزودن namespace اپلیکیشن
$autoloader->addNamespace('App\\', 'app/src/');

// افزودن namespace تست
$autoloader->addNamespace('App\\Tests\\', 'tests/');

// ── ۴) بارگذاری helperهای اپلیکیشن (در صورت وجود) ─────────────────────────
$helpersFile = TVN_BASE_PATH . '/app/src/helpers.php';
if (is_file($helpersFile)) {
    require_once $helpersFile;
}

// ── ۵) بارگذاری .env (در صورت وجود) ───────────────────────────────────────
$envFile = TVN_BASE_PATH . '/.env';
if (is_file($envFile) && is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                $_SERVER[$key] = $value;
                $_ENV[$key] = $value;
            }
        }
    }
}

return $autoloader;
