<?php
declare(strict_types=1);

namespace App\Domain\Category;

interface CategoryRepository
{
    public function all(): CategoryCollection;

    public function find(CategoryId $id): ?Category;
}
