<?php

namespace App\Enums;

class AuthProvider
{
    public const EMAIL = 'email';
    public const GOOGLE = 'google';

    public static function all(): array
    {
        return [self::EMAIL, self::GOOGLE];
    }

    public static function isValid(string $provider): bool
    {
        return in_array($provider, self::all(), true);
    }
}
