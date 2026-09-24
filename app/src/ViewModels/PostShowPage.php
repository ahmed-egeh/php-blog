<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\ValueObjects\Post;

final readonly class PostShowPage
{
    public function __construct(
        public string $title,
        public Post $post,
        public bool $isOwner,
    ) {}
}
