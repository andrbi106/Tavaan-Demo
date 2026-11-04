<?php

/**
 * تَوان | Tavaan — Bootstrap (هستهٔ فریمورک)
 *
 * نقطهٔ ورود فریمورک. این فایل تنها require دستی مجاز در Entry Pointهاست.
 *
 * زنجیره:
 *   ثابت‌ها → Autoloader → Namespace mappings → config/autoload (در صورت وجود)
 */

declare(strict_types=1);

// ── ۰) idempotent ─────────────────────────────────────────────────────────
if (defined('TVN_BOOTSTRAPPED')) {
    return;
}

// ── ۱) ثابت‌ها ────────────────────────────────────────────────────────────
defined('TVN_VERSION') || define('TVN_VERSION', '0.22.0');
defined('TVN_START') || define('TVN_START', microtime(true));
defined('TVN_BASE_PATH') || define('TVN_BASE_PATH', dirname(__DIR__));

// ── ۲) حداقل نسخهٔ PHP ──────────────────────────────────────────────────────
if (PHP_VERSION_ID < 80200) {
    fwrite(STDERR, 'Tavaan requires PHP >= 8.2.0. Current: ' . PHP_VERSION . PHP_EOL);
    exit(1);
}

// ── ۳) تنظیمات اولیهٔ خطا ───────────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// ── ۴) Autoloader ─────────────────────────────────────────────────────────
require_once __DIR__ . '/Autoloading/Autoloader.php';
require_once __DIR__ . '/Autoloading/AutoloaderRegistry.php';

use Tavaan\Core\Autoloading\Autoloader;
use Tavaan\Core\Autoloading\AutoloaderRegistry;

$autoloader = Autoloader::create(TVN_BASE_PATH);

// نگاشت namespaceهای پایه (PSR-4)
$autoloader->addNamespace('Tavaan\\Core\\', __DIR__ . '/');
$autoloader->addNamespace('Tavaan\\Support\\', __DIR__ . '/Support/');
$autoloader->addNamespace('Tavaan\\Engine\\', 'engines/*/src/');
$autoloader->addNamespace('Tavaan\\EngineCore\\', 'vendor/andrbi106/tavaan-engine-core/src/');

$autoloader->register();
$autoloader->lockPrefix('Tavaan\\Core\\');
$autoloader->lockPrefix('Tavaan\\Engine\\');

AutoloaderRegistry::set($autoloader);

// ── ۵) بارگذاری نگاشت کامل از config (اگر موجود) ───────────────────────────
$configFile = TVN_BASE_PATH . '/config/autoload.php';
if (is_file($configFile)) {
    /** @var array<string, mixed> $autoloadConfig */
    $autoloadConfig = require $configFile;

    foreach ($autoloadConfig['psr-4'] ?? [] as $prefix => $paths) {
        $autoloader->addNamespace((string) $prefix, $paths);
    }

    foreach ($autoloadConfig['locked_prefixes'] ?? [] as $locked) {
        $autoloader->lockPrefix((string) $locked);
    }
}

// ── ۶) ثبت در کانتینر ─────────────────────────────────────────────────────
define('TVN_BOOTSTRAPPED', true);

return $autoloader;
