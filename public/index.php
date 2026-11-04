<?php

/**
 * تَوان | Tavaan — HTTP Entry Point (Demo v0.0.1)
 */

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Tavaan\Core\Application\ApplicationBuilder;

$app = ApplicationBuilder::create(TVN_BASE_PATH)->boot();

// ─── مسیریابی ساده v0.0.1 ─────────────────────────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// ─── کشف موتورها ──────────────────────────────────────────────────────────
$enginesPath = TVN_BASE_PATH . '/engines';
$discoveredEngines = [];
$engineConfigs = $GLOBALS['tvn_engine_configs'] ?? [];

if (is_dir($enginesPath)) {
    foreach (new \DirectoryIterator($enginesPath) as $entry) {
        if ($entry->isDot() || !$entry->isDir()) continue;
        $ej = $entry->getPathname() . '/engine.json';
        if (is_file($ej)) {
            $data = json_decode(file_get_contents($ej), true);
            if (is_array($data)) {
                $discoveredEngines[] = [
                    'id'       => $entry->getFilename(),
                    'name'     => $data['name'] ?? $entry->getFilename(),
                    'title'    => $data['title_fa'] ?? '',
                    'version'  => $data['version'] ?? '0.0.0',
                    'class'    => $data['class'] ?? '',
                    'has_config' => isset($engineConfigs[$entry->getFilename()]),
                ];
            }
        }
    }
    sort($discoveredEngines);
}

$engineCount = count($discoveredEngines);
$configCount = count($engineConfigs);
$tvnVersion = TVN_VERSION;
$phpVersion = PHP_VERSION;

// ─── GET / — صفحهٔ خوش‌آمدگویی ─────────────────────────────────────────────
if ($uri === '/' || $uri === '') {
    header('Content-Type: text/html; charset=utf-8');

    $engineList = '';
    foreach ($discoveredEngines as $e) {
        $cfgIcon = $e['has_config'] ? ' ⚙' : '';
        $engineList .= "<li>{$e['id']}{$cfgIcon}</li>";
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>تَوان | Tavaan v{$tvnVersion}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Tahoma,sans-serif;background:#1a1a2e;color:#e0e0e0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
.c{max-width:800px;text-align:center}
h1{font-size:3rem;color:#4a90d9;margin:0}
.v{display:inline-block;background:#16213e;color:#4a90d9;padding:.3rem 1.2rem;border-radius:999px;font-size:1.1rem;margin:1rem 0}
.g{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin:2rem 0}
.card{background:#16213e;border-radius:12px;padding:1.5rem;border:1px solid #0f3460}
.card .n{font-size:2.2rem;color:#e8732c;font-weight:bold}
.card .l{font-size:.85rem;color:#888;margin-top:.5rem}
.engines{text-align:right;background:#16213e;border-radius:12px;padding:1.5rem;margin-top:1rem}
.engines h3{color:#4a90d9;margin:0 0 1rem;font-size:1.1rem}
.engines ul{list-style:none;display:flex;flex-wrap:wrap;gap:.4rem}
.engines li{background:#0f3460;padding:.2rem .7rem;border-radius:6px;font-size:.8rem}
.f{margin-top:2rem;color:#555;font-size:.85rem}
a{color:#4a90d9;text-decoration:none}
.cfg{color:#e8a200}
</style>
</head>
<body><div class="c">
<h1>تَوان</h1>
<div class="v">نسخهٔ {$tvnVersion}</div>
<div class="g">
<div class="card"><div class="n">{$engineCount}</div><div class="l">موتور کشف‌شده</div></div>
<div class="card"><div class="n">{$configCount}</div><div class="l">پیکربندی موتور</div></div>
<div class="card"><div class="n">۳</div><div class="l">درگاه ارتباط</div></div>
<div class="card"><div class="n">{$phpVersion}</div><div class="l">نسخهٔ PHP</div></div>
</div>
<div class="engines"><h3>موتورهای کشف‌شده (⚙ = دارای پیکربندی)</h3><ul>{$engineList}</ul></div>
<div class="f">
تَوان | Tavaan — فریمورک PHP 8.2+ فارسی‌اول<br>
<a href="https://github.com/andrbi106/Tavaan-Demo">GitHub</a> ·
<a href="/api">API</a> ·
<a href="/info">PHP Info</a> ·
<a href="/engines">موتورها</a>
</div>
</div></body></html>
HTML;
    exit;
}

// ─── GET /api — وضعیت سیستم (JSON) ────────────────────────────────────────
if ($uri === '/api') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'framework' => 'Tavaan',
        'version' => $tvnVersion,
        'php' => $phpVersion,
        'engines_count' => $engineCount,
        'configs_count' => $configCount,
        'gateways' => ['Container', 'Events', 'EngineBus'],
        'engines' => array_map(fn($e) => $e['id'], $discoveredEngines),
        'status' => 'running',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── GET /engines — لیست موتورها ──────────────────────────────────────────
if ($uri === '/engines') {
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html><html lang='fa' dir='rtl'><head><meta charset='UTF-8'><title>موتورها</title>";
    echo "<style>body{font-family:Tahoma,sans-serif;background:#1a1a2e;color:#e0e0e0;padding:2rem}";
    echo "table{width:100%;border-collapse:collapse}th,td{padding:.5rem 1rem;text-align:right;border-bottom:1px solid #333}";
    echo "th{color:#4a90d9}.cfg{color:#e8a200}a{color:#4a90d9}</style></head><body>";
    echo "<h1 style='color:#4a90d9'>موتورهای تَوان v{$tvnVersion}</h1>";
    echo "<p style='color:#888;margin:1rem 0'>⚠ = دارای پیکربندی</p>";
    echo "<table><tr><th>شناسه</th><th>نام</th><th>عنوان</th><th>نسخه</th><th>پیکربندی</th></tr>";
    foreach ($discoveredEngines as $e) {
        $cfg = $e['has_config'] ? '⚙ بله' : '—';
        echo "<tr><td>{$e['id']}</td><td>{$e['name']}</td><td>{$e['title']}</td><td>{$e['version']}</td><td>{$cfg}</td></tr>";
    }
    echo "</table><p style='margin-top:2rem'><a href='/'>→ خانه</a></p></body></html>";
    exit;
}

// ─── GET /info — PHP info ─────────────────────────────────────────────────
if ($uri === '/info') {
    header('Content-Type: text/html; charset=utf-8');
    phpinfo();
    exit;
}

// ─── 404 ──────────────────────────────────────────────────────────────────
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html lang='fa' dir='rtl'><head><meta charset='UTF-8'><title>۴۰۴</title>";
echo "<style>body{font-family:Tahoma;background:#1a1a2e;color:#e0e0e0;display:flex;align-items:center;justify-content:center;min-height:100vh}";
echo "a{color:#4a90d9}</style></head><body>";
echo "<div style='text-align:center'><h1 style='color:#e8732c'>۴۰۴</h1><p>صفحه یافت نشد</p><p style='margin-top:2rem'><a href='/'>→ خانه</a></p></div>";
echo "</body></html>";
