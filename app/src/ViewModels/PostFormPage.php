<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Domain\Category\CategoryCollection;
use App\Domain\Post\Post;

final readonly class PostFormPage extends ViewModel
{
    public function __construct(
        string $title,
        public string $heading,
        public string $action,
        public ?Post $post,
        public CategoryCollection $categories,
    ) {
        parent::__construct($title);
    }
}
