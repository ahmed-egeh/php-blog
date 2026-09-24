<?php
declare(strict_types=1);

namespace App\Infrastructure;

use RuntimeException;

final class Env
{
    public static function string(string $key): string
    {
        $value = $_ENV[$key] ?? getenv($key);

        if (!is_string($value) || $value === '') {
            throw new RuntimeException("Missing environment variable: {$key}");
        }

        return $value;
    }

    public static function int(string $key): int
    {
        return (int) self::string($key);
    }
}
