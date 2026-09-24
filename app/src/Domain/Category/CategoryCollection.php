<?php
declare(strict_types=1);

namespace App\Domain\Category;

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
