<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Domain\Post\Post;

final readonly class PostShowPage extends ViewModel
{
    public function __construct(
        string $title,
        public Post $post,
        public bool $isOwner,
    ) {
        parent::__construct($title);
    }
}
