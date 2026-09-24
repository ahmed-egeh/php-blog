<?php
declare(strict_types=1);

namespace App\Http;

final class Request
{
    public static function path(): string
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $path = rtrim((string) $path, '/');

        return $path === '' ? '/' : $path;
    }

    public static function method(): string
    {
        return (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function string(string $key): string
    {
        return trim((string) ($_POST[$key] ?? ''));
    }

    public static function int(string $key): int
    {
        return (int) ($_POST[$key] ?? 0);
    }

    public static function query(string $key): string
    {
        return (string) ($_GET[$key] ?? '');
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public static function cookie(string $key): string
    {
        return (string) ($_COOKIE[$key] ?? '');
    }

    public static function file(string $key): ?UploadedFile
    {
        return UploadedFile::fromRequest($key);
    }
}
