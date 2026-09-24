<?php
declare(strict_types=1);

namespace App\Domain\Category;

final readonly class Category
{
    public function __construct(
        public CategoryId $id,
        public CategoryName $name,
    ) {}
}
