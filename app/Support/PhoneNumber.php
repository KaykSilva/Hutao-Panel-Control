<?php

namespace App\Support;

class PhoneNumber
{
    public static function digits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }

    /**
     * @return array<int, string>
     */
    public static function variants(?string $value): array
    {
        $digits = static::digits($value);

        if ($digits === '') {
            return [];
        }

        $variants = [$digits];

        if (str_starts_with($digits, '55')) {
            $local = substr($digits, 2);
            $variants[] = $local;
            $variants[] = static::toggleBrazilMobileNinthDigit($digits, 2);
            $variants[] = static::toggleBrazilMobileNinthDigit($local, 0);
        } else {
            $withCountryCode = '55'.$digits;
            $variants[] = $withCountryCode;
            $variants[] = static::toggleBrazilMobileNinthDigit($digits, 0);
            $variants[] = static::toggleBrazilMobileNinthDigit($withCountryCode, 2);
        }

        return array_values(array_filter(array_unique($variants)));
    }

    private static function toggleBrazilMobileNinthDigit(string $digits, int $countryCodeLength): ?string
    {
        $local = substr($digits, $countryCodeLength);

        if (strlen($local) === 11 && $local[2] === '9') {
            return substr($digits, 0, $countryCodeLength + 2).substr($digits, $countryCodeLength + 3);
        }

        if (strlen($local) === 10) {
            return substr($digits, 0, $countryCodeLength + 2).'9'.substr($digits, $countryCodeLength + 2);
        }

        return null;
    }
}
