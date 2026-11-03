<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

/**
 * MimeDetector — تشخیص MIME از magic bytes (با fallback).
 */
final class MimeDetector
{
    /**
     * @var array<string, list<array{0: string, 1: int}>>
     */
    private const array SIGNATURES = [
        'image/jpeg' => [["\xFF\xD8\xFF", 0]],
        'image/png' => [["\x89PNG\r
\x1A
", 0]],
        'image/gif' => [['GIF87a', 0], ['GIF89a', 0]],
        'image/webp' => [['RIFF', 0]],
        'image/bmp' => [['BM', 0]],
        'application/pdf' => [['%PDF-', 0]],
        'application/zip' => [["PK\x03\x04", 0]],
        'application/gzip' => [["\x1F\x8B", 0]],
        'video/mp4' => [['ftypisom', 4], ['ftypmp42', 4]],
        'audio/mpeg' => [['ID3', 0]],
    ];

    /**
     * نقشهٔ extension به MIME برای تطبیق.
     *
     * @var array<string, string>
     */
    private const array EXT_MIME = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png', 'gif' => 'image/gif',
        'webp' => 'image/webp', 'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml',
        'pdf' => 'application/pdf',
        'zip' => 'application/zip', 'gz' => 'application/gzip',
        'mp4' => 'video/mp4',
        'mp3' => 'audio/mpeg',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'json' => 'application/json',
        'xml' => 'application/xml',
    ];

    public function detect(string $path, ?string $extension = null): ?string
    {
        // 1. Try magic bytes
        $contents = @\file_get_contents($path, false, null, 0, 64);
        if (\is_string($contents)) {
            foreach (self::SIGNATURES as $mime => $sigs) {
                foreach ($sigs as [$needle, $offset]) {
                    if (\substr($contents, $offset, \strlen($needle)) === $needle) {
                        return $mime;
                    }
                }
            }
        }
        // 2. Fallback to extension
        $ext = $extension ?? \pathinfo($path, \PATHINFO_EXTENSION);
        return self::EXT_MIME[\strtolower((string) $ext)] ?? null;
    }

    /**
     * تطبیق extension و MIME (برای امنیت آپلود).
     */
    public function extensionMatchesMime(string $extension, string $mime): bool
    {
        $expected = self::EXT_MIME[\strtolower($extension)] ?? null;
        return $expected === $mime;
    }

    /**
     * لیست extensionهای مجاز.
     *
     * @return list<string>
     */
    public function allowedExtensions(): array
    {
        return \array_keys(self::EXT_MIME);
    }
}
