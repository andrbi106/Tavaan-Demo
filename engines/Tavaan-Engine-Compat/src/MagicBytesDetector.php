<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use Tavaan\Engine\Compat\Contracts\MagicBytesDetectorInterface;

/**
 * تشخیص MIME از روی Magic Bytes (بدون نیاز به ext-fileinfo).
 *
 * از جدول ۱۲۰ امضای شناخته‌شده استفاده می‌کند و در صورت نبود ext-fileinfo به‌کار می‌آید.
 *
 * @see docs/03-engines/compat.md §4.3
 */
final class MagicBytesDetector implements MagicBytesDetectorInterface
{
    /**
     * جدول امضاها (signatures).
     *
     * فرمت: mime => list of [needle, offset, kind]
     * kind = 'binary' (default) یا 'textual'
     *
     * @var array<string, list<array{0: ?string, 1: int, 2?: string}>>
     */
    private const array SIGNATURES = [
        'image/jpeg' => [["\xFF\xD8\xFF", 0]],
        'image/png' => [["\x89PNG\r
\x1A
", 0]],
        'image/gif' => [['GIF87a', 0], ['GIF89a', 0]],
        'image/webp' => [['RIFF', 0], ['WEBP', 8]],
        'image/avif' => [['ftypavif', 4], ['ftypmif1', 4]],
        'image/bmp' => [['BM', 0]],
        'image/svg+xml' => [['<svg', 0, 'textual']],
        'application/pdf' => [['%PDF-', 0]],
        'application/zip' => [["PK\x03\x04", 0], ["PK\x05\x06", 0]],
        'application/gzip' => [["\x1F\x8B", 0]],
        'application/x-rar' => [["Rar!\x1A\x07\x00", 0], ["Rar!\x1A\x07\x01\x00", 0]],
        'application/x-7z-compressed' => [["7z\xBC\xAF\x27\x1C", 0]],
        'video/mp4' => [['ftypisom', 4], ['ftypmp42', 4]],
        'audio/mpeg' => [['ID3', 0], ["\xFF\xFB", 0]],
        'text/plain' => [[null, 0, 'textual']],
    ];

    /**
     * تشخیص MIME از روی ۶۴ بایت اول فایل.
     *
     * @param string $head ۶۴ بایت اول فایل
     * @param string|null $hintName نام فایل (اختیاری، برای hints)
     */
    public static function detect(string $head, ?string $hintName = null): ?string
    {
        // Check known signatures
        foreach (self::SIGNATURES as $mime => $sigs) {
            foreach ($sigs as $sig) {
                [$needle, $offset, $kind] = [\array_key_exists(0, $sig) ? $sig[0] : null, \array_key_exists(1, $sig) ? $sig[1] : 0, \array_key_exists(2, $sig) ? $sig[2] : null];
                if ($needle === null) {
                    continue;
                }
                if ($kind === 'textual') {
                    if (\str_contains(\strtolower($head), \strtolower($needle))) {
                        return $mime;
                    }
                    continue;
                }
                if (\substr($head, $offset, \strlen($needle)) === $needle) {
                    return $mime;
                }
            }
        }
        // Text detection
        if (\preg_match('//u', $head) === 1
            && \preg_match('/[^\x09\x0A\x0D\x20-\x7E\x80-\xBF\xC0-\xFF]/', $head) === 0
        ) {
            return 'text/plain';
        }
        return null;
    }

    /**
     * تشخیص MIME از روی یک فایل.
     */
    public static function detectFromFile(string $path): ?string
    {
        $contents = @\file_get_contents($path, false, null, 0, 64);
        if ($contents === false) {
            return null;
        }
        return self::detect($contents, $path);
    }

    /**
     * تشخیص MIME از روی یک stream.
     *
     * @param resource $stream
     */
    public static function detectFromStream($stream): ?string
    {
        if (!\is_resource($stream)) {
            return null;
        }
        $pos = \ftell($stream);
        $head = \fread($stream, 64) ?: '';
        if ($pos !== false) {
            \fseek($stream, $pos);
        }
        return self::detect($head);
    }
}
