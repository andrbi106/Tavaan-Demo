<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Support;

/** Service Provider که بارگذاری آن می‌تواند به تعویق بیفتد (lazy). */
interface DeferrableProvider
{
    /** @return list<string>  سرویس‌هایی که این provider ارائه می‌دهد */
    public function provides(): array;
}
