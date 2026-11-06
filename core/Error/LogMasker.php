<?php
declare(strict_types=1);
namespace Tavaan\Core\Error;

/**
 * ماسک‌کنندهٔ دادهٔ حساس در لاگ.
 * رمزهای عبور، توکن‌ها، کلیدها را در context لاگ ماسک می‌کند.
 */
final class LogMasker
{
    /** @var list<string> کلیدهایی که ماسک می‌شوند */
    private array $sensitiveKeys = [
        'password', 'passwd', 'pwd',
        'token', 'access_token', 'refresh_token', 'api_key', 'apikey',
        'secret', 'private_key', 'privateKey',
        'credit_card', 'creditCard', 'cvv',
        'ssn', 'national_id', 'nationalId',
    ];

    /** @var string مقدار جایگزین برای دادهٔ حساس */
    private string $mask = '***';

    /**
     * ماسک کردن دادهٔ حساس در یک آرایه.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function mask(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($this->isSensitive($key)) {
                $result[$key] = $this->mask;
            } elseif (is_array($value)) {
                $result[$key] = $this->mask($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function isSensitive(string $key): bool
    {
        $lower = strtolower($key);
        foreach ($this->sensitiveKeys as $sensitive) {
            if ($lower === $sensitive || str_contains($lower, $sensitive)) {
                return true;
            }
        }
        return false;
    }

    public function addSensitiveKey(string $key): void
    {
        $this->sensitiveKeys[] = strtolower($key);
    }
}
