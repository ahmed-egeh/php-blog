<?php
declare(strict_types=1);

namespace App\Application\Post;

use App\Domain\Post\PostCollection;

final readonly class PagedPosts
{
    public function __construct(
        public PostCollection $posts,
        public int $page,
        public int $perPage,
        public int $total,
    ) {}

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }
}
