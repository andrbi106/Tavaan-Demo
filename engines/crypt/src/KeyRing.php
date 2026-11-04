<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt;

/**
 * KeyRing — مدیریت نسخه‌های کلید رمزنگاری برای rotation.
 *
 * اجازه می‌دهد چندین نسخه کلید همزمان موجود باشد:
 * - نسخه فعلی برای رمزنگاری جدید
 * - نسخه‌های قدیمی برای رمزگشایی داده‌های قدیمی
 *
 * @see docs/03-engines/crypt.md §5
 */
final class KeyRing
{
    /**
     * @var array<int, string> نسخه => کلید (32 bytes)
     */
    private array $keys = [];

    private int $currentVersion = 1;

    public function __construct(
        string $currentKey,
        int $version = 1,
    ) {
        if (\strlen($currentKey) !== 32) {
            throw new \InvalidArgumentException('AES-256 key must be 32 bytes.');
        }
        $this->keys[$version] = $currentKey;
        $this->currentVersion = $version;
    }

    /**
     * افزودن کلید قدیمی (برای رمزگشایی داده‌های قدیمی).
     */
    public function addKey(int $version, string $key): self
    {
        if (\strlen($key) !== 32) {
            throw new \InvalidArgumentException('AES-256 key must be 32 bytes.');
        }
        $this->keys[$version] = $key;
        return $this;
    }

    /**
     * چرخش کلید: نسخه جدید به‌عنوان current و قبلی حفظ می‌شود.
     */
    public function rotate(string $newKey): self
    {
        $this->currentVersion++;
        $this->addKey($this->currentVersion, $newKey);
        return $this;
    }

    public function key(int $version): ?string
    {
        return $this->keys[$version] ?? null;
    }

    public function currentVersion(): int
    {
        return $this->currentVersion;
    }

    public function currentKey(): string
    {
        return $this->keys[$this->currentVersion];
    }

    /**
     * @return array<int, string>
     */
    public function allKeys(): array
    {
        return $this->keys;
    }

    /**
     * تولید کلید تصادفی 32 bytes.
     */
    public static function generateKey(): string
    {
        return \random_bytes(32);
    }
}
