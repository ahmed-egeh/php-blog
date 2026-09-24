<?php
declare(strict_types=1);

namespace App\Collections;

use App\ValueObjects\Category;
use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Category> */
final class CategoryCollection implements IteratorAggregate, Countable
{
    /** @var list<Category> */
    private array $items;

    public function __construct(Category ...$items)
    {
        $this->items = $items;
    }

    public static function fromRows(iterable $rows): self
    {
        $categories = new self();

        foreach ($rows as $row) {
            if (!is_object($row)) {
                continue;
            }

            $categories->items[] = Category::fromRow($row);
        }

        return $categories;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }
}
