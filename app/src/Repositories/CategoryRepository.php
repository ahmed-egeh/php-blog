<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function __construct(
        private Category $model = new Category(),
    ) {}

    public function all(): array
    {
        return $this->model->all();
    }

    public function find(int $id): array|false
    {
        return $this->model->find($id);
    }
}
