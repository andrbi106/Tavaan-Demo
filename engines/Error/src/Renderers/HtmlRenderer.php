<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error\Renderers;

use Tavaan\Engine\Error\HttpException;

/**
 * HtmlRenderer — خروجی خطا به‌صورت HTML (صفحهٔ خطای ساده).
 */
final class HtmlRenderer
{
    public function render(HttpException $e, bool $debug = false): string
    {
        $title = $this->titleFor($e->statusCode);
        $message = $this->escape($e->getMessage());
        $refCode = $e->refCode ?? '';
        $debugSection = '';
        if ($debug) {
            $prev = $e->getPrevious();
            if ($prev !== null) {
                $debugSection = "<h3>Debug</h3><pre>"
                    . $this->escape(\get_class($prev)) . ": "
                    . $this->escape($prev->getMessage()) . "

"
                    . $this->escape($prev->getTraceAsString()) . "</pre>";
            }
        }
        $refCodeHtml = $refCode !== '' ? "<p>Ref: <code>{$refCode}</code></p>" : '';
        return <<<HTML
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>{$title} ({$e->statusCode})</title>
<style>
body { font-family: Tahoma, sans-serif; max-width: 600px; margin: 100px auto; padding: 20px; color: #333; }
h1 { color: #c00; }
pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
code { background: #eee; padding: 2px 4px; }
</style>
</head>
<body>
<h1>{$title} ({$e->statusCode})</h1>
<p>{$message}</p>
{$refCodeHtml}
{$debugSection}
</body>
</html>
HTML;
    }

    private function titleFor(int $status): string
    {
        return match ($status) {
            404 => 'صفحه یافت نشد',
            403 => 'دسترسی ممنوع',
            401 => 'احراز هویت لازم است',
            500 => 'خطای سرور',
            503 => 'سرویس در دسترس نیست',
            default => 'خطا',
        };
    }

    private function escape(string $s): string
    {
        return \htmlspecialchars($s, \ENT_QUOTES, 'UTF-8');
    }
}
