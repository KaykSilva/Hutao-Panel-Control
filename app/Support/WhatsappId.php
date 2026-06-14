<?php

namespace App\Support;

class WhatsappId
{
    public static function normalize(?string $value): string
    {
        $user = explode('@', (string) $value)[0] ?? '';
        $user = explode(':', $user)[0] ?? '';

        return preg_replace('/\D+/', '', $user) ?? '';
    }

    /**
     * @return array<int, string>
     */
    public static function variants(?string $value): array
    {
        $normalized = static::normalize($value);

        if ($normalized === '') {
            return [];
        }

        return array_values(array_unique([
            $normalized,
            $normalized.'@lid',
            $normalized.'@s.whatsapp.net',
        ]));
    }
}
