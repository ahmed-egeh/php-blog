<?php
declare(strict_types=1);

namespace App\Http;

use App\Domain\Post\Post;

final class AssetUrl
{
    public static function postImage(Post $post): string
    {
        $image = $post->image !== null ? trim($post->image) : '';
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/post-' . (($post->id->value % 6) + 1) . '.svg';
    }

    public static function avatar(?string $image): string
    {
        $image = $image !== null ? trim($image) : '';
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/avatar.svg';
    }

    public static function isCurrentRoute(string $route): bool
    {
        return Request::path() === $route;
    }
}
