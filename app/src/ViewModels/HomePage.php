<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Collections\PostCollection;

final readonly class HomePage
{
    public function __construct(
        public string $title,
        public PostCollection $posts,
    ) {}
}
