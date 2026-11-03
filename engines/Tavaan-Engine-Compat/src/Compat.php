<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use Tavaan\Engine\Compat\Contracts\CompatInterface;

/**
 * موتور Compat — سازگاری و Polyfill.
 *
 * فراهم‌کردن پیاده‌سازی خالص PHP برای افزونه‌ها و توابعی که ممکن است موجود نباشند،
 * تا اصل «خودکفایی» حفظ شود و موتورهای دیگر بدون `if (extension_loaded)` پراکنده بنویسند.
 *
 * 🔒 قاعده: کد موتور باید `Compat::xxx()` را صدا بزند؛ تشخیص و Fallback در این موتور متمرکز است.
 *
 * @see docs/03-engines/compat.md (DOC-083E)
 */
final class Compat implements CompatInterface
{
    /**
     * پیکربندی موتور.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function has(string $extension): bool
    {
        return \extension_loaded($extension);
    }

    public function hasAny(array $extensions): bool
    {
        foreach ($extensions as $ext) {
            if ($this->has($ext)) {
                return true;
            }
        }
        return false;
    }

    public function require(string $extension, string $capability): void
    {
        if (!$this->has($extension)) {
            throw EngineCapabilityException::forExtension($extension, $capability);
        }
    }

    public function report(): array
    {
        $extensions = [
            'mbstring', 'bcmath', 'openssl', 'sodium', 'zip', 'zlib', 'curl',
            'gd', 'imagick', 'intl', 'fileinfo', 'exif', 'apcu', 'dom',
            'xmlwriter', 'pcntl', 'posix', 'opcache', 'sockets', 'redis',
            'memcached', 'pdo', 'pdo_mysql', 'pdo_pgsql', 'pdo_sqlite',
        ];
        $report = [];
        foreach ($extensions as $ext) {
            $report[$ext] = [
                'required' => false,
                'loaded' => $this->has($ext),
                'fallback' => $this->fallbackFor($ext),
            ];
        }
        return $report;
    }

    public function driver(string $capability): string
    {
        $drivers = $this->config['drivers'] ?? [];
        $setting = $drivers[$capability] ?? 'auto';

        return match ($capability) {
            'image' => $this->resolveImageDriver($setting),
            'zip' => $this->resolveZipDriver($setting),
            'http' => $this->resolveHttpDriver($setting),
            'math' => $this->resolveMathDriver($setting),
            'unicode' => $this->resolveUnicodeDriver($setting),
            'mime' => $this->resolveMimeDriver($setting),
            default => 'none',
        };
    }

    public function version(): string
    {
        return PHP_VERSION;
    }

    private function resolveImageDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'none';
        }
        if ($this->has('imagick')) {
            return 'imagick';
        }
        if ($this->has('gd')) {
            return 'gd';
        }
        return 'none';
    }

    private function resolveZipDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'none';
        }
        return $this->has('zip') ? 'zip' : 'pure';
    }

    private function resolveHttpDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'stream';
        }
        return $this->has('curl') ? 'curl' : 'stream';
    }

    private function resolveMathDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'decimal';
        }
        if ($this->has('bcmath')) {
            return 'bcmath';
        }
        if ($this->has('gmp')) {
            return 'gmp';
        }
        return 'decimal';
    }

    private function resolveUnicodeDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'native';
        }
        if ($this->has('intl')) {
            return 'intl';
        }
        if ($this->has('mbstring')) {
            return 'mbstring';
        }
        return 'native';
    }

    private function resolveMimeDriver(string $setting): string
    {
        if ($setting !== 'auto') {
            return $this->has($setting) ? $setting : 'magic';
        }
        return $this->has('fileinfo') ? 'fileinfo' : 'magic';
    }

    private function fallbackFor(string $extension): string
    {
        return match ($extension) {
            'mbstring' => 'Utf8 (regex-based)',
            'bcmath' => 'Decimal (pure PHP long arithmetic)',
            'openssl' => 'sodium (if available)',
            'sodium' => 'openssl EVP',
            'zip' => 'ZipWriterPure (Store + Deflate)',
            'zlib' => 'Store (no compression)',
            'curl' => 'Stream HTTP transport',
            'gd', 'imagick' => 'no processing (metadata only)',
            'intl' => 'Jalali, PersianNumber, PluralRules (self-written)',
            'fileinfo' => 'MagicBytesDetector (120 signatures)',
            'exif' => 'JPEG APP1 parser (self-written)',
            'apcu' => 'file/array cache',
            'dom', 'xmlwriter' => 'Simple HTML/Text converter',
            'pcntl' => 'polling (no signals)',
            'posix' => 'getmypid(), getmyuid()',
            'opcache' => 'no-op',
            'sockets' => 'stream_socket_server',
            'redis', 'memcached' => 'array/file cache',
            default => 'none',
        };
    }
}
