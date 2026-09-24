<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Domain\Post\PostCollection;

final readonly class HomePage extends ViewModel
{
    public function __construct(
        string $title,
        public PostCollection $posts,
    ) {
        parent::__construct($title);
    }
}
