<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Collections\CategoryCollection;
use App\Models\Category as CategoryModel;
use App\ValueObjects\Category;
use App\ValueObjects\CategoryId;

class CategoryRepository
{
    public function __construct(
        private CategoryModel $model = new CategoryModel(),
    ) {}

    public function all(): CategoryCollection
    {
        return CategoryCollection::fromRows($this->model->all());
    }

    public function find(CategoryId $id): ?Category
    {
        $row = $this->model->find($id->value);

        return $row === false ? null : Category::fromRow($row);
    }
}
