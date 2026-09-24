<?php
declare(strict_types=1);

namespace App\Domain\Post;

use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Post> */
final class PostCollection implements IteratorAggregate, Countable
{
    /** @var list<Post> */
    private array $items;

    public function __construct(Post ...$items)
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
