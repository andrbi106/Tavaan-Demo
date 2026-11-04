<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Support;

/** شیء‌ای که قابل تبدیل به آرایه است. */
interface Arrayable
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
