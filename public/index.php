<?php

/**
 * تَوان | Tavaan — HTTP Entry Point (Demo v0.0.1)
 */

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Tavaan\Core\Application\ApplicationBuilder;

$app = ApplicationBuilder::create(TVN_BASE_PATH)->boot();

// مسیریابی ساده
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// کشف موتورها
$enginesPath = TVN_BASE_PATH . '/engines';
$discoveredEngines = [];
if (is_dir($enginesPath)) {
    foreach (new \DirectoryIterator($enginesPath) as $entry) {
        if ($entry->isDot() || !$entry->isDir()) continue;
        if (is_file($entry->getPathname() . '/engine.json')) {
            $discoveredEngines[] = $entry->getFilename();
        }
    }
}

if ($uri === '/' || $uri === '') {
    header('Content-Type: text/html; charset=utf-8');
    $ver = '0.0.1';
    $php = PHP_VERSION;
    $count = count($discoveredEngines);
    $engines_list = implode('', array_map(fn($e) => "<li>$e</li>", $discoveredEngines));

    echo <<<HTML
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>تَوان | Tavaan v{$ver}</title>
<style>
body{font-family:Tahoma,sans-serif;background:#1a1a2e;color:#e0e0e0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:2rem}
.c{max-width:700px;text-align:center}
h1{font-size:3rem;color:#4a90d9;margin:0}
.v{display:inline-block;background:#16213e;color:#4a90d9;padding:.25rem 1rem;border-radius:999px;font-size:1.1rem;margin:1rem 0}
.g{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin:2rem 0}
.card{background:#16213e;border-radius:12px;padding:1.5rem;border:1px solid #0f3460}
.card .n{font-size:2rem;color:#e8732c;font-weight:bold}
.card .l{font-size:.85rem;color:#888;margin-top:.5rem}
.engines{text-align:right;background:#16213e;border-radius:12px;padding:1.5rem;margin-top:1rem}
.engines h3{color:#4a90d9;margin:0 0 1rem}
.engines ul{list-style:none;display:flex;flex-wrap:wrap;gap:.5rem;padding:0;margin:0}
.engines li{background:#0f3460;padding:.25rem .75rem;border-radius:6px;font-size:.8rem}
.f{margin-top:2rem;color:#555;font-size:.85rem}
a{color:#4a90d9}
</style></head><body><div class="c">
<h1>تَوان</h1><div class="v">نسخهٔ {$ver}</div>
<div class="g">
<div class="card"><div class="n">{$count}</div><div class="l">موتور</div></div>
<div class="card"><div class="n">۲</div><div class="l">مخزن مرکزی</div></div>
<div class="card"><div class="n">۳</div><div class="l">درگاه ارتباط</div></div>
<div class="card"><div class="n">PHP {$php}</div><div class="l">نسخهٔ PHP</div></div>
</div>
<div class="engines"><h3>موتورهای کشف‌شده:</h3><ul>{$engines_list}</ul></div>
<div class="f">تَوان | Tavaan — فریمورک PHP 8.2+ فارسی‌اول<br>
<a href="https://github.com/andrbi106/Tavaan-Demo">GitHub</a> · <a href="/api">API</a> · <a href="/info">PHP Info</a></div>
</div></body></html>
HTML;
    exit;
}

if ($uri === '/api') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'framework' => 'Tavaan', 'version' => '0.0.1', 'php' => PHP_VERSION,
        'engines_count' => count($discoveredEngines),
        'gateways' => ['Container', 'Events', 'EngineBus'], 'status' => 'running',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($uri === '/info') { header('Content-Type: text/html; charset=utf-8'); phpinfo(); exit; }

http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
echo '<html lang="fa" dir="rtl"><body><h1>۴۰۴</h1><p><a href="/">خانه</a></p></body></html>';
