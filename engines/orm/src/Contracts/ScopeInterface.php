<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM\Contracts;

use Tavaan\Engine\Database\Contracts\QueryBuilderInterface;

/**
 * تَوان | Tavaan — Contracts
 * ScopeInterface — قرارداد Global Scope (DOC-050 §6).
 *
 * @api 1.0
 * @see docs/03-engines/orm.md
 */
interface ScopeInterface
{
    /**
     * اعمال محدودیت روی کوئری (مثل TenantScope/OwnershipScope).
     *
     * @param QueryBuilderInterface $query
     * @param ModelInterface        $model
     */
    public function apply(QueryBuilderInterface $query, ModelInterface $model): void;
}
