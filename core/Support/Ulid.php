<?php
declare(strict_types=1);

namespace Tavaan\Support;

/**
 * ULID (Universally Unique Lexicographically Sortable Identifier).
 *
 * 26 کاراکتر، sortable، یکتا.
 */
final class Ulid
{
    private const ENCODE = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    private const LENGTH = 26;

    /**
     * تولید ULID جدید.
     */
    public static function generate(): string
    {
        $time = (int) (microtime(true) * 1000);
        $timeBytes = '';
        for ($i = 0; $i < 6; $i++) {
            $timeBytes .= chr(($time >> (40 - $i * 8)) & 0xFF);
        }

        $randomBytes = random_bytes(10);
        $bytes = $timeBytes . $randomBytes;

        return self::encode($bytes);
    }

    private static function encode(string $bytes): string
    {
        $result = '';
        for ($i = 0; $i < 16; $i++) {
            $byte = ord($bytes[$i]);
            $result .= self::ENCODE[($byte >> 5) & 0x1F];
            $result .= self::ENCODE[$byte & 0x1F];
        }
        // Trim to 26 chars
        return substr($result, 0, self::LENGTH);
    }
}
