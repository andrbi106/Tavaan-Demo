<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use Tavaan\Engine\Compat\Contracts\ZipWriterPureInterface;

/**
 * نویسنده ZIP خالص PHP (بدون نیاز به ext-zip).
 *
 * از روش‌های Deflate (در صورت موجود بودن gzdeflate) یا Store استفاده می‌کند.
 * فایل‌های تولیدشده با `unzip`/`7z`/`ext-zip` سازگار هستند.
 *
 * 🔒 تست با بازکردن ZIP تولیدشده توسط ext-zip و مقایسه CRC.
 *
 * @see docs/03-engines/compat.md §4.2
 */
final class ZipWriterPure implements ZipWriterPureInterface
{
    /**
     * @var list<array{name: string, crc: int, size: int, csize: int, method: int, offset: int}>
     */
    private array $entries = [];
    private string $buffer = '';

    /**
     * افزودن یک فایل از محتوای رشته‌ای.
     *
     * @param string $name نام فایل در ZIP
     * @param string $data محتوای فایل
     * @param int $level سطح فشرده‌سازی (0-9, 0 = Store)
     */
    public function addFromString(string $name, string $data, int $level = 6): void
    {
        $compressed = (\function_exists('gzdeflate') && $level > 0)
            ? \gzdeflate($data, $level)
            : false;
        $method = 0; // 0 = Store, 8 = Deflate
        $final = $data;
        if ($compressed !== false && \strlen($compressed) < \strlen($data)) {
            $method = 8;
            $final = $compressed;
        }
        $offset = \strlen($this->buffer);
        $crc = \crc32($data);
        $size = \strlen($data);
        $csize = \strlen($final);

        // Local File Header
        $this->buffer .= "PK\x03\x04"
            . \pack('v', 20)        // version needed to extract (2.0)
            . \pack('v', 0)         // general purpose bit flag
            . \pack('v', $method)   // compression method
            . \pack('v', 0)         // last mod file time
            . \pack('v', 0)         // last mod file date
            . \pack('V', $crc)      // CRC-32
            . \pack('V', $csize)    // compressed size
            . \pack('V', $size)    // uncompressed size
            . \pack('v', \strlen($name)) // file name length
            . \pack('v', 0)         // extra field length
            . $name
            . $final;

        $this->entries[] = [
            'name' => $name,
            'crc' => $crc,
            'size' => $size,
            'csize' => $csize,
            'method' => $method,
            'offset' => $offset,
        ];
    }

    /**
     * افزودن یک فایل از مسیر روی دیسک.
     */
    public function addFile(string $path, ?string $name = null): void
    {
        $contents = @\file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }
        $this->addFromString($name ?? \basename($path), $contents);
    }

    /**
     * افزودن یک دایرکتوری (با slash انتهایی به‌عنوان directory entry).
     */
    public function addDirectory(string $name): void
    {
        if (!\str_ends_with($name, '/')) {
            $name .= '/';
        }
        // Directory entry — empty data, Store method
        $offset = \strlen($this->buffer);
        $crc = 0;
        $size = 0;
        $csize = 0;
        $method = 0;

        $this->buffer .= "PK\x03\x04"
            . \pack('v', 20)
            . \pack('v', 0)
            . \pack('v', $method)
            . \pack('v', 0)
            . \pack('v', 0)
            . \pack('V', $crc)
            . \pack('V', $csize)
            . \pack('V', $size)
            . \pack('v', \strlen($name))
            . \pack('v', 0)
            . $name;

        $this->entries[] = [
            'name' => $name,
            'crc' => $crc,
            'size' => $size,
            'csize' => $csize,
            'method' => $method,
            'offset' => $offset,
        ];
    }

    /**
     * تولید بایت‌های کامل فایل ZIP.
     */
    public function output(): string
    {
        $cd = '';
        foreach ($this->entries as $e) {
            $cd .= "PK\x01\x02"
                . \pack('v', 20)             // version made by
                . \pack('v', 20)             // version needed
                . \pack('v', 0)              // general purpose bit flag
                . \pack('v', $e['method'])   // compression method
                . \pack('v', 0)              // last mod time
                . \pack('v', 0)              // last mod date
                . \pack('V', $e['crc'])      // CRC-32
                . \pack('V', $e['csize'])    // compressed size
                . \pack('V', $e['size'])     // uncompressed size
                . \pack('v', \strlen($e['name'])) // file name length
                . \pack('v', 0)              // extra field length
                . \pack('v', 0)              // file comment length
                . \pack('v', 0)              // disk number start
                . \pack('v', 0)              // internal file attributes
                . \pack('V', 0)              // external file attributes
                . \pack('V', $e['offset'])   // offset of local header
                . $e['name'];
        }
        $cdOffset = \strlen($this->buffer);
        $cdSize = \strlen($cd);

        // End of Central Directory Record
        $eocd = "PK\x05\x06"
            . \pack('v', 0)                       // number of this disk
            . \pack('v', 0)                       // disk where central directory starts
            . \pack('v', \count($this->entries))  // number of CD records on this disk
            . \pack('v', \count($this->entries))  // total number of CD records
            . \pack('V', $cdSize)                 // size of central directory
            . \pack('V', $cdOffset)               // offset of start of CD
            . \pack('v', 0);                      // comment length

        return $this->buffer . $cd . $eocd;
    }

    /**
     * ذخیره ZIP در یک فایل.
     */
    public function saveTo(string $path): void
    {
        $result = @\file_put_contents($path, $this->output());
        if ($result === false) {
            throw new \RuntimeException("Failed to write ZIP file: {$path}");
        }
    }

    /**
     * بازنشاندن writer برای ساختن یک ZIP جدید.
     */
    public function reset(): void
    {
        $this->entries = [];
        $this->buffer = '';
    }
}
