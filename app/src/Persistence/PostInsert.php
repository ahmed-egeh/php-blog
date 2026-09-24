<?php
declare(strict_types=1);

namespace App\Persistence;

final readonly class PostInsert
{
    public function __construct(
        public int $user_id,
        public string $title,
        public string $content,
        public int $active,
        public ?string $image,
        public int $category_id,
    ) {}
}
