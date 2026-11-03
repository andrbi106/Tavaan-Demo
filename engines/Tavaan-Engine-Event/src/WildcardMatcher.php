<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event;

/**
 * WildcardMatcher — تطبیق الگوی wildcard (مثل `shop.order.*`).
 *
 * الگوریتم Trie-based با segment split on '.'.
 */
final class WildcardMatcher
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $trie = [];

    /**
     * افزودن یک الگو.
     *
     * @param string $pattern الگو مثل 'shop.order.*' یا 'shop.order.paid'
     * @param callable $listener
     */
    public function add(string $pattern, callable $listener): void
    {
        $segments = \explode('.', $pattern);
        $current = &$this->trie;
        foreach ($segments as $i => $segment) {
            $isLast = $i === \count($segments) - 1;
            if ($segment === '*') {
                $current['__wildcard_listeners'][] = $listener;
                return;
            }
            if (!isset($current[$segment])) {
                $current[$segment] = ['__listeners' => []];
            }
            if ($isLast) {
                $current[$segment]['__listeners'][] = $listener;
                return;
            }
            $current = &$current[$segment];
        }
    }

    /**
     * یافتن همه‌ی listenerهای مطابق با نام رویداد.
     *
     * @param string $event نام رویداد مثل 'shop.order.paid'
     * @return list<callable>
     */
    public function match(string $event): array
    {
        $listeners = [];
        $segments = \explode('.', $event);
        $current = $this->trie;
        foreach ($segments as $i => $segment) {
            // Wildcard matches everything below
            if (isset($current['__wildcard_listeners'])) {
                foreach ($current['__wildcard_listeners'] as $l) {
                    $listeners[] = $l;
                }
            }
            if (!isset($current[$segment])) {
                return $listeners;
            }
            $current = $current[$segment];
            $isLast = $i === \count($segments) - 1;
            if ($isLast) {
                if (isset($current['__listeners'])) {
                    foreach ($current['__listeners'] as $l) {
                        $listeners[] = $l;
                    }
                }
                if (isset($current['__wildcard_listeners'])) {
                    foreach ($current['__wildcard_listeners'] as $l) {
                        $listeners[] = $l;
                    }
                }
            }
        }
        return $listeners;
    }

    /**
     * پاک‌کردن همه‌ی الگوها.
     */
    public function clear(): void
    {
        $this->trie = [];
    }

    /**
     * پاک‌کردن همه‌ی listenerهای یک الگو.
     */
    public function remove(string $pattern): void
    {
        $segments = \explode('.', $pattern);
        $current = &$this->trie;
        $path = [];
        foreach ($segments as $i => $segment) {
            $isLast = $i === \count($segments) - 1;
            if ($segment === '*') {
                unset($current['__wildcard_listeners']);
                return;
            }
            if (!isset($current[$segment])) {
                return;
            }
            $path[] = [$segment, &$current];
            if ($isLast) {
                unset($current[$segment]);
                return;
            }
            $current = &$current[$segment];
        }
    }
}
