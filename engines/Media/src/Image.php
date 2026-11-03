<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

use GdImage;

/**
 * Image — GD-based image manipulation wrapper.
 */
final class Image
{
    private GdImage|false $gd;
    private string $mime = 'image/jpeg';
    private string $path = '';

    public function __construct(string $path = '')
    {
        $this->path = $path;
        if ($path !== '' && file_exists($path)) {
            $info = @getimagesize($path);
            if ($info !== false) {
                $this->mime = $info['mime'];
                $this->gd = match ($this->mime) {
                    'image/jpeg' => imagecreatefromjpeg($path),
                    'image/png' => imagecreatefrompng($path),
                    'image/gif' => imagecreatefromgif($path),
                    'image/webp' => imagecreatefromwebp($path),
                    default => false,
                };
                if ($this->gd !== false) {
                    imagealphablending($this->gd, true);
                    imagesavealpha($this->gd, true);
                }
            } else {
                $this->gd = imagecreatetruecolor(1, 1);
            }
        } else {
            $this->gd = imagecreatetruecolor(1, 1);
        }
    }

    /**
     * make — create a new blank image (or load from path).
     */
    public function make(?string $path = null, ?int $width = null, ?int $height = null): self
    {
        if ($path !== null) {
            $this->path = $path;
            $info = @getimagesize($path);
            if ($info !== false) {
                $this->mime = $info['mime'];
                $this->gd = match ($this->mime) {
                    'image/jpeg' => imagecreatefromjpeg($path),
                    'image/png' => imagecreatefrompng($path),
                    'image/gif' => imagecreatefromgif($path),
                    'image/webp' => imagecreatefromwebp($path),
                    default => false,
                };
                if ($this->gd !== false) { return $this; }
            }
        }
        if ($width !== null && $height !== null) {
            $this->gd = imagecreatetruecolor($width, $height);
            imagefill($this->gd, 0, 0, imagecolorallocatealpha($this->gd, 255, 255, 255, 127));
        }
        return $this;
    }

    /**
     * resize — scale image to given dimensions.
     */
    public function resize(int $width, int $height = 0, bool $maintainRatio = true): self
    {
        if ($this->gd === false) { return $this; }
        $origW = imagesx($this->gd);
        $origH = imagesy($this->gd);
        if ($maintainRatio && $height === 0) {
            $ratio = $origW / $origH;
            $height = (int) round($width / $ratio);
        } elseif ($maintainRatio) {
            $ratio = min($width / $origW, $height / $origH);
            $width = (int) round($origW * $ratio);
            $height = (int) round($origH * $ratio);
        }
        $new = imagecreatetruecolor($width, $height);
        imagealphablending($new, false);
        imagesavealpha($new, true);
        imagecopyresampled($new, $this->gd, 0, 0, 0, 0, $width, $height, $origW, $origH);
        $this->gd = $new;
        return $this;
    }

    /**
     * crop — extract a region from the image.
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): self
    {
        if ($this->gd === false) { return $this; }
        $new = imagecrop($this->gd, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
        if ($new !== false) {
            $this->gd = $new;
        }
        return $this;
    }

    /**
     * thumbnail — shortcut for resize to small dimensions (max side).
     */
    public function thumbnail(int $size): self
    {
        if ($this->gd === false) { return $this; }
        $origW = imagesx($this->gd);
        $origH = imagesy($this->gd);
        if ($origW > $origH) {
            return $this->resize($size, 0, true);
        }
        return $this->resize(0, $size, true);
    }

    /**
     * watermark — overlay a second image onto current image.
     */
    public function watermark(string $watermarkPath, int $opacity = 100, string $position = 'bottom-right'): self
    {
        if ($this->gd === false || !file_exists($watermarkPath)) { return $this; }
        $overlay = imagecreatefrompng($watermarkPath);
        if ($overlay === false) { return $this; }
        $ow = imagesx($overlay);
        $oh = imagesy($overlay);
        $cw = imagesx($this->gd);
        $ch = imagesy($this->gd);
        $x = match ($position) {
            'top-left' => 0,
            'top-right' => $cw - $ow,
            'bottom-left' => 0,
            'center' => (int) (($cw - $ow) / 2),
            default => $cw - $ow,
        };
        $y = match ($position) {
            'top-left' => 0,
            'top-right' => 0,
            'bottom-left' => $ch - $oh,
            'center' => (int) (($ch - $oh) / 2),
            default => $ch - $oh,
        };
        imagecopymerge($this->gd, $overlay, $x, $y, 0, 0, $ow, $oh, $opacity);
        imagedestroy($overlay);
        return $this;
    }

    /**
     * convert — change format by updating mime type.
     */
    public function convert(string $format): self
    {
        $this->mime = match (strtolower($format)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => $this->mime,
        };
        return $this;
    }

    /**
     * rotate — rotate image by given angle.
     */
    public function rotate(float $angle, int $bgColor = 0): self
    {
        if ($this->gd === false) { return $this; }
        // GD uses counter-clockwise; flip sign to make clockwise convention.
        $rotated = imagerotate($this->gd, -$angle, $bgColor);
        if ($rotated !== false) {
            $this->gd = $rotated;
        }
        return $this;
    }

    /**
     * flip — mirror horizontally or vertically.
     */
    public function flip(string $mode = 'horizontal'): self
    {
        if ($this->gd === false) { return $this; }
        $flipType = match ($mode) {
            'vertical' => IMG_FLIP_VERTICAL,
            'both' => IMG_FLIP_BOTH,
            default => IMG_FLIP_HORIZONTAL,
        };
        imageflip($this->gd, $flipType);
        return $this;
    }

    /**
     * save — write the image to disk in the current mime format.
     */
    public function save(?string $path = null, ?int $quality = null): self
    {
        if ($this->gd === false) { return $this; }
        $path ??= $this->path;
        if ($path === '') { return $this; }
        $q = $quality ?? 90;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $saved = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($this->gd, $path, $q),
            'png' => imagepng($this->gd, $path, (int) round(($q ?? 90) / 10)),
            'gif' => imagegif($this->gd, $path),
            'webp' => imagewebp($this->gd, $path, $q),
            default => imagejpeg($this->gd, $path, $q),
        };
        if ($saved) { $this->path = $path; }
        return $this;
    }

    /**
     * exif — return EXIF metadata (only for JPEG).
     * @return array<string, mixed>
     */
    public function exif(): array
    {
        if ($this->path === '' || !file_exists($this->path)) { return []; }
        if (function_exists('exif_read_data') && $this->mime === 'image/jpeg') {
            return @exif_read_data($this->path) ?: [];
        }
        return [];
    }

    /**
     * Get GD resource (testing/inspection).
     */
    public function getResource(): GdImage|false
    {
        return $this->gd;
    }

    public function getWidth(): int
    {
        return $this->gd === false ? 0 : imagesx($this->gd);
    }

    public function getHeight(): int
    {
        return $this->gd === false ? 0 : imagesy($this->gd);
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function __destruct()
    {
        if ($this->gd instanceof GdImage) {
            imagedestroy($this->gd);
        }
    }
}
