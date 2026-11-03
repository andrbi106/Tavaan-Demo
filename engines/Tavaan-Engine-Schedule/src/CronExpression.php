<?php

declare(strict_types=1);

namespace Tavaan\Engine\Schedule;

/**
 * CronExpression — parser for 5-field cron syntax.
 */
final class CronExpression
{
    private array $fields;

    public function __construct(private readonly string $expression = '* * * * *')
    {
        $parts = preg_split('/\s+/', trim($expression)) ?: [];
        if (count($parts) !== 5) {
            $parts = ['*', '*', '*', '*', '*'];
        }
        $this->fields = $parts;
    }

    public function isDue(\DateTime $now = null): bool
    {
        $now ??= new \DateTime();
        $parts = [
            (int) $now->format('i'),
            (int) $now->format('H'),
            (int) $now->format('d'),
            (int) $now->format('m'),
            (int) $now->format('w'),
        ];
        foreach ($this->fields as $i => $field) {
            if (!$this->matchField($field, $parts[$i], $i)) {
                return false;
            }
        }
        return true;
    }

    /**
     * nextRunDates — compute the next N run dates after $from.
     * @return list<\DateTime>
     */
    public function nextRunDates(int $count = 1, ?\DateTime $from = null): array
    {
        $from ??= new \DateTime();
        $from = clone $from;
        $from->modify('+1 minute');
        $from->setTime((int) $from->format('H'), (int) $from->format('i'), 0);

        $dates = [];
        $iter = clone $from;
        $limit = 0;
        while (count($dates) < $count && $limit < 525600) { // max 1 year worth of minutes
            if ($this->isDue($iter)) {
                $dates[] = clone $iter;
            }
            $iter->modify('+1 minute');
            $limit++;
        }
        return $dates;
    }

    private function matchField(string $field, int $value, int $fieldIndex): bool
    {
        if ($field === '*') { return true; }
        foreach (explode(',', $field) as $part) {
            $part = trim($part);
            $step = 1;
            if (str_contains($part, '/')) {
                [$part, $stepStr] = explode('/', $part, 2);
                $step = (int) $stepStr;
            }
            if ($part === '*') {
                if ($value % $step === 0) { return true; }
                continue;
            }
            if (str_contains($part, '-')) {
                [$min, $max] = array_map('intval', explode('-', $part, 2));
                if ($value >= $min && $value <= $max && (($value - $min) % $step === 0)) { return true; }
                continue;
            }
            if ((int) $part === $value) { return true; }
        }
        return false;
    }

    public function getExpression(): string { return $this->expression; }
}
