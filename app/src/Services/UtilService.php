<?php
declare(strict_types=1);

namespace App\Services;

use App\Http\Request;
use App\ValueObjects\Post;

class UtilService
{
    public static function isCurrentRoute(string $route): bool
    {
        return Request::path() === $route;
    }

    public static function postImageUrl(Post $post): string
    {
        $image = $post->image !== null ? trim($post->image) : '';
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/post-' . (($post->id->value % 6) + 1) . '.svg';
    }

    public static function avatarUrl(?string $image): string
    {
        $image = $image !== null ? trim($image) : '';
        if ($image !== '') {
            return '/' . ltrim($image, '/');
        }

        return '/images/illustrations/avatar.svg';
    }
}
