<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media;

/**
 * MediaManager — manage file uploads and media variants.
 */
final class MediaManager
{
    /** @var array<string, mixed> */
    private array $config;
    private string $uploadDir;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->uploadDir = $config['upload_dir'] ?? '/tmp/media';
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * upload — accept an uploaded file array ($_FILES['name']) and store it.
     * @param array<string, mixed> $file
     * @return array<string, mixed>|null
     */
    public function upload(array $file, ?string $subDir = null): ?array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        $original = basename($file['name'] ?? 'upload.bin');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
        $ext = pathinfo($safeName, PATHINFO_EXTENSION);
        $basename = pathinfo($safeName, PATHINFO_FILENAME);
        $stored = $basename . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dir = $this->uploadDir . ($subDir ? '/' . trim($subDir, '/') : '');
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $dest = $dir . '/' . $stored;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }
        return [
            'path' => $dest,
            'name' => $stored,
            'original' => $original,
            'mime' => $file['type'] ?? mime_content_type($dest),
            'size' => filesize($dest) ?: 0,
        ];
    }

    /**
     * fromPath — load an existing file as a media record.
     * @return array<string, mixed>|null
     */
    public function fromPath(string $path): ?array
    {
        if (!file_exists($path)) { return null; }
        return [
            'path' => $path,
            'name' => basename($path),
            'original' => basename($path),
            'mime' => mime_content_type($path) ?: 'application/octet-stream',
            'size' => filesize($path) ?: 0,
        ];
    }

    /**
     * variants — generate predefined size variants of an image.
     * @param array<int, array{name: string, width?: int, height?: int}> $specs
     * @return array<string, string>
     */
    public function variants(string $imagePath, array $specs = []): array
    {
        if (!file_exists($imagePath)) { return []; }
        $specs = $specs !== [] ? $specs : [
            ['name' => 'thumbnail', 'width' => 150],
            ['name' => 'medium', 'width' => 480],
            ['name' => 'large', 'width' => 1024],
        ];
        $results = [];
        $base = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . pathinfo($imagePath, PATHINFO_FILENAME);
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        foreach ($specs as $spec) {
            $name = $spec['name'];
            $width = $spec['width'] ?? 100;
            $height = $spec['height'] ?? 0;
            $img = new Image();
            $img->make($imagePath)->resize($width, $height, true);
            $out = "{$base}_{$name}.{$ext}";
            $img->save($out);
            $results[$name] = $out;
        }
        return $results;
    }
}
