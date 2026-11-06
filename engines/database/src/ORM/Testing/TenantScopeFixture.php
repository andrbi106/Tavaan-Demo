<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM\Testing;

use Tavaan\Engine\Database\Contracts\QueryBuilderInterface;
use Tavaan\Engine\Database\ORM\Contracts\ModelInterface;
use Tavaan\Engine\Database\ORM\Contracts\ScopeInterface;

/**
 * Fixture تست ORM — Global Scope چنداجاره‌ای (T-08).
 *
 * @api internal
 */
final class TenantScopeFixture implements ScopeInterface
{
    public static int $currentTenant = 1;

    public function apply(QueryBuilderInterface $query, ModelInterface $model): void
    {
        $query->where('tenant_id', self::$currentTenant);
    }
}
