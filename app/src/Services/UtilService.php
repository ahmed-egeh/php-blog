<?php
declare(strict_types=1);

namespace App\Services;

class UtilService
{
    public static function isCurrentRoute(string $route): bool
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === $route;
    }

    public static function postImageUrl(?string $image, int $postId): string
    {
        $image = trim((string) $image);
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/post-' . (($postId % 6) + 1) . '.svg';
    }

    public static function avatarUrl(?string $image): string
    {
        $image = trim((string) $image);
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/avatar.svg';
    }
}
