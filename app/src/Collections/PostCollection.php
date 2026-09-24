<?php
declare(strict_types=1);

namespace App\Collections;

use App\ValueObjects\Post;
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

    public static function fromRows(iterable $rows): self
    {
        $posts = new self();

        foreach ($rows as $row) {
            $posts->items[] = Post::fromRow($row);
        }

        return $posts;
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
