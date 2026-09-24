<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Category
{
    public function __construct(
        public CategoryId $id,
        public CategoryName $name,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id: new CategoryId((int) $row->id),
            name: new CategoryName((string) $row->name),
        );
    }
}
