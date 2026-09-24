<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Collections\PostCollection;

final readonly class PostsIndexPage
{
    public function __construct(
        public string $title,
        public string $heading,
        public PostCollection $posts,
    ) {}
}
