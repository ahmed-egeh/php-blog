<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryCollection;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryName;
use PDOStatement;

final class CategoryMapper
{
    public static function fromRow(object $row): Category
    {
        return new Category(
            id: new CategoryId((int) $row->id),
            name: new CategoryName((string) $row->name),
        );
    }

    public static function collection(PDOStatement $statement): CategoryCollection
    {
        $categories = [];

        while ($row = $statement->fetch()) {
            if (is_object($row)) {
                $categories[] = self::fromRow($row);
            }
        }

        return new CategoryCollection(...$categories);
    }
}
