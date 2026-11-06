<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

/**
 * تَوان | Tavaan — Tenancy
 * TenantStrategyInterface — قرارداد مدیر اتصال استراتژی B/C.
 *
 * @api 1.0
 * @see docs/03-engines/tenancy.md
 */
interface TenantStrategyInterface
{
    /** برقراری اتصال/زمینه برای tenant. */
    public function connect(\Tavaan\Engine\Tenancy\Contracts\TenantInterface $tenant): void;

    /** بازگشت به حالت مرکزی. */
    public function disconnect(): void;

    /** ساخت فضای دادهٔ tenant (DB یا Schema). */
    public function provision(\Tavaan\Engine\Tenancy\Contracts\TenantInterface $tenant): void;

    /** حذف فضای دادهٔ tenant. */
    public function deprovision(\Tavaan\Engine\Tenancy\Contracts\TenantInterface $tenant): void;

    public function name(): string;
}
