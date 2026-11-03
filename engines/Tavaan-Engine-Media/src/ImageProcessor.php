<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

use Tavaan\Engine\Media\Exceptions\MediaException;

/**
 * ImageProcessor — پردازش تصویر با fallback به GD.
 *
 * 🔒 هیچ نیازی به ext-gd یا ext-imagick ندارد؛ در صورت نبود آن‌ها، فقط metadata برمی‌گردد.
 */
final class ImageProcessor
{
    public function __construct(
        private readonly string $driver = 'auto',
    ) {
    }

    public function driver(): string
    {
        if ($this->driver !== 'auto') return $this->driver;
        if (\extension_loaded('imagick')) return 'imagick';
        if (\extension_loaded('gd')) return 'gd';
        return 'none';
    }

    /**
     * خواندن ابعاد تصویر.
     *
     * @return array{width: int, height: int, type: string}|null
     */
    public function dimensions(string $path): ?array
    {
        if (\function_exists('getimagesize')) {
            $info = @\getimagesize($path);
            if (\is_array($info) && $info[0] > 0) {
                return [
                    'width' => $info[0],
                    'height' => $info[1],
                    'type' => $info[2] === \IMAGETYPE_JPEG ? 'jpeg' : ($info[2] === \IMAGETYPE_PNG ? 'png' : 'unknown'),
                ];
            }
        }
        return null;
    }

    /**
     * تغییر اندازه تصویر.
     *
     * @return bool آیا تغییر موفق بود
     */
    public function resize(string $source, string $destination, int $width, int $height, bool $keepAspect = true): bool
    {
        if (!\function_exists('imagecreatetruecolor')) {
            throw MediaException::processingFailed('GD extension required for image processing');
        }
        $srcInfo = $this->dimensions($source);
        if ($srcInfo === null) {
            throw MediaException::processingFailed('cannot read source image');
        }
        $srcImg = $this->createImageFromFile($source, $srcInfo['type']);
        if ($srcImg === null) {
            throw MediaException::processingFailed('cannot create image resource');
        }
        $destImg = \imagecreatetruecolor($width, $height);
        if ($destImg === false) {
            throw MediaException::processingFailed('cannot create destination image');
        }
        // Preserve transparency for PNG
        if ($srcInfo['type'] === 'png') {
            \imagealphablending($destImg, false);
            \imagesavealpha($destImg, true);
        }
        if ($keepAspect) {
            $srcRatio = $srcInfo['width'] / $srcInfo['height'];
            $dstRatio = $width / $height;
            if ($srcRatio > $dstRatio) {
                $newWidth = $srcInfo['width'];
                $newHeight = (int) ($srcInfo['width'] / $dstRatio);
                $srcX = 0;
                $srcY = (int) (($srcInfo['height'] - $newHeight) / 2);
            } else {
                $newHeight = $srcInfo['height'];
                $newWidth = (int) ($srcInfo['height'] * $dstRatio);
                $srcX = (int) (($srcInfo['width'] - $newWidth) / 2);
                $srcY = 0;
            }
            \imagecopyresampled($destImg, $srcImg, 0, 0, $srcX, $srcY, $width, $height, $newWidth, $newHeight);
        } else {
            \imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcInfo['width'], $srcInfo['height']);
        }
        $result = $this->saveImage($destImg, $destination, $srcInfo['type']);
        \imagedestroy($srcImg);
        \imagedestroy($destImg);
        return $result;
    }

    /**
     * تولید thumbnail.
     */
    public function thumbnail(string $source, string $destination, int $size = 150): bool
    {
        return $this->resize($source, $destination, $size, $size, keepAspect: true);
    }

    /**
     * @return resource|\GdImage|null
     */
    private function createImageFromFile(string $path, string $type)
    {
        return match ($type) {
            'jpeg' => @\imagecreatefromjpeg($path) ?: null,
            'png' => @\imagecreatefrompng($path) ?: null,
            'gif' => @\imagecreatefromgif($path) ?: null,
            default => null,
        };
    }

    private function saveImage($image, string $path, string $type): bool
    {
        return match ($type) {
            'jpeg' => \imagejpeg($image, $path, quality: 85),
            'png' => \imagepng($image, $path),
            'gif' => \imagegif($image, $path),
            default => false,
        };
    }
}
