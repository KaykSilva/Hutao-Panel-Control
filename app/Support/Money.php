<?php

namespace App\Support;

class Money
{
    public static function toCents(string|int|float|null $value): int
    {
        $raw = trim((string) $value);
        $normalized = str_contains($raw, ',')
            ? str_replace(['.', ','], ['', '.'], $raw)
            : $raw;

        return (int) round(((float) $normalized) * 100);
    }

    public static function toDecimal(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
