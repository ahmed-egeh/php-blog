<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Collections\CategoryCollection;
use App\ValueObjects\Post;

final readonly class PostFormPage
{
    public function __construct(
        public string $title,
        public string $heading,
        public string $action,
        public ?Post $post,
        public CategoryCollection $categories,
    ) {}
}
