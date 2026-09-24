<?php
declare(strict_types=1);

namespace App;

use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Route> */
final class RouteList implements IteratorAggregate
{
    /** @var list<Route> */
    private array $routes = [];

    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function getIterator(): Traversable
    {
        yield from $this->routes;
    }
}
