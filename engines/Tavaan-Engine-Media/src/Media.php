<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

/**
 * Media — رکورد یک فایل رسانه‌ای.
 */
final class Media
{
    public function __construct(
        public readonly string $id,
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly array $metadata = [],
        public readonly ?string $disk = null,
        public readonly ?string $url = null,
    ) {
    }

    public function isImage(): bool
    {
        return \str_starts_with($this->mimeType, 'image/');
    }

    public function isVideo(): bool
    {
        return \str_starts_with($this->mimeType, 'video/');
    }

    public function isAudio(): bool
    {
        return \str_starts_with($this->mimeType, 'audio/');
    }

    public function extension(): string
    {
        $ext = \pathinfo($this->path, \PATHINFO_EXTENSION);
        return \is_string($ext) ? $ext : '';
    }

    public function filename(): string
    {
        return \basename($this->path);
    }

    public function width(): ?int
    {
        return $this->metadata['width'] ?? null;
    }

    public function height(): ?int
    {
        return $this->metadata['height'] ?? null;
    }

    /**
     * Generate a unique ID for media (random hex).
     */
    public static function generateId(): string
    {
        return \bin2hex(\random_bytes(16));
    }
}
