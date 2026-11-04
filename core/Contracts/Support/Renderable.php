<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Support;

/** شیء‌ای که قابل render به string است. */
interface Renderable
{
    public function render(): string;
}
