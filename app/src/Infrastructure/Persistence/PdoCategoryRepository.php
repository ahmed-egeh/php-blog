<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryCollection;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use PDO;
use PDOStatement;

final class PdoCategoryRepository implements CategoryRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): CategoryCollection
    {
        $statement = $this->pdo->query('SELECT * FROM categories');

        if (!$statement instanceof PDOStatement) {
            return new CategoryCollection();
        }

        return CategoryMapper::collection($statement);
    }

    public function find(CategoryId $id): ?Category
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM categories
            WHERE id = :id
            LIMIT 1
        ");
        $statement->bindValue('id', $id->value, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        return is_object($row) ? CategoryMapper::fromRow($row) : null;
    }
}
