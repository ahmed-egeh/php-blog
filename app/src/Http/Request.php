<?php
declare(strict_types=1);

namespace App\Http;

final class Request
{
    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url(is_string($uri) ? $uri : '/', PHP_URL_PATH);
        $path = is_string($path) ? rtrim($path, '/') : '';

        return $path === '' ? '/' : $path;
    }

    public static function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return is_string($method) && $method !== '' ? $method : 'GET';
    }

    public static function string(string $key): string
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    public static function int(string $key): int
    {
        $value = $_POST[$key] ?? 0;

        return is_numeric($value) ? (int) $value : 0;
    }

    public static function query(string $key): string
    {
        $value = $_GET[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public static function cookie(string $key): string
    {
        $value = $_COOKIE[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    public static function file(string $key): ?UploadedFile
    {
        return UploadedFile::fromRequest($key);
    }
}
