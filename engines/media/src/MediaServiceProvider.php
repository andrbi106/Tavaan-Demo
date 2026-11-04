<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

/**
 * MediaServiceProvider — ثبت موتور Media.
 */
final class MediaServiceProvider
{
    private ?MimeDetector $detector = null;
    private ?Uploader $uploader = null;
    private ?ImageProcessor $processor = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->detector = new MimeDetector();
        $this->uploader = new Uploader(
            detector: $this->detector,
            maxSize: $this->config['max_size'] ?? 104857600,
            allowedExtensions: $this->config['allowed_extensions'] ?? [],
        );
        $this->processor = new ImageProcessor(
            driver: $this->config['image_driver'] ?? 'auto',
        );
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [MimeDetector::class, Uploader::class, ImageProcessor::class, Media::class];
    }

    public function getDetector(): MimeDetector
    {
        if ($this->detector === null) $this->register();
        return $this->detector;
    }

    public function getUploader(): Uploader
    {
        if ($this->uploader === null) $this->register();
        return $this->uploader;
    }

    public function getProcessor(): ImageProcessor
    {
        if ($this->processor === null) $this->register();
        return $this->processor;
    }
}
