<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Port\AppUrl;
use RuntimeException;

final class EnvAppUrl implements AppUrl
{
    private string $base;

    public function __construct(string $base)
    {
        $normalized = rtrim($base, '/');
        $host = parse_url($normalized, PHP_URL_HOST);

        if ($normalized === '' || !is_string($host) || $host === '') {
            throw new RuntimeException('APP_URL must be an absolute URL with a host.');
        }

        $this->base = $normalized;
    }

    public function to(string $path): string
    {
        return $this->base . '/' . ltrim($path, '/');
    }
}
