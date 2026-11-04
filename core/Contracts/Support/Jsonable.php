<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Support;

/** شیء‌ای که قابل تبدیل به JSON است. */
interface Jsonable
{
    /** @return string JSON representation */
    public function toJson(int $options = 0): string;
}
