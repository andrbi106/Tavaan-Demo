<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

use Tavaan\Engine\Media\Exceptions\MediaException;

/**
 * Uploader — آپلود امن فایل‌های رسانه‌ای.
 *
 * 🔒 مراحل:
 *   1. اعتبارسنجی size
 *   2. تشخیص MIME از magic bytes (نه content-type کاربر)
 *   3. تطبیق extension ↔ MIME (جلوگیری از .php با image/*)
 *   4. rename با random (حذف نام اصلی)
 *   5. ذخیره خارج از DocumentRoot
 *
 * @see docs/03-engines/media.md
 */
final class Uploader
{
    public function __construct(
        private readonly MimeDetector $detector = new MimeDetector(),
        private readonly int $maxSize = 104857600,  // 100MB
        /**
         * @var list<string>
         */
        private readonly array $allowedExtensions = [],
    ) {
    }

    /**
     * آپلود یک فایل.
     *
     * @param string $sourcePath مسیر فایل موقت
     * @param string $originalName نام اصلی فایل (برای استخراج extension)
     * @param string $destinationDir مسیر مقصد
     * @return Media رکورد فایل آپلود‌شده
     */
    public function upload(string $sourcePath, string $originalName, string $destinationDir): Media
    {
        if (!\file_exists($sourcePath)) {
            throw MediaException::uploadFailed('source file not found');
        }
        $size = (int) \filesize($sourcePath);
        if ($size > $this->maxSize) {
            throw MediaException::fileTooLarge($size, $this->maxSize);
        }
        $ext = \strtolower(\pathinfo($originalName, \PATHINFO_EXTENSION));
        if (!\is_string($ext) || $ext === '') {
            throw MediaException::uploadFailed('no extension');
        }
        if (!empty($this->allowedExtensions) && !\in_array($ext, $this->allowedExtensions, true)) {
            throw MediaException::uploadFailed("extension '{$ext}' not allowed");
        }
        $mime = $this->detector->detect($sourcePath, $ext);
        if ($mime === null) {
            throw MediaException::uploadFailed('cannot detect MIME');
        }
        if (!$this->detector->extensionMatchesMime($ext, $mime)) {
            throw MediaException::extensionMismatch($ext, $mime);
        }
        // Random name
        $newName = \bin2hex(\random_bytes(16)) . ".{$ext}";
        $destPath = \rtrim($destinationDir, '/') . '/' . $newName;
        if (!\is_dir(\dirname($destPath))) {
            \mkdir(\dirname($destPath), 0777, true);
        }
        if (!\copy($sourcePath, $destPath)) {
            throw MediaException::uploadFailed('failed to copy file');
        }
        return new Media(
            id: Media::generateId(),
            path: $destPath,
            mimeType: $mime,
            size: $size,
            metadata: ['original_name' => $originalName],
        );
    }

    /**
     * اعتبارسنجی یک فایل بدون آپلود (برای تست).
     */
    public function validate(string $sourcePath, string $originalName): array
    {
        $size = (int) \filesize($sourcePath);
        $ext = \strtolower((string) \pathinfo($originalName, \PATHINFO_EXTENSION));
        $mime = $this->detector->detect($sourcePath, $ext);
        return [
            'size' => $size,
            'extension' => $ext,
            'mime' => $mime,
            'valid' => $mime !== null && $this->detector->extensionMatchesMime($ext, $mime ?? ''),
        ];
    }
}
