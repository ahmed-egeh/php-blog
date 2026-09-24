<?php
declare(strict_types=1);

namespace App;

final readonly class Route
{
    public function __construct(
        public string $method,
        public string $path,
        public Action $action,
        public MiddlewareQueue $middlewares,
    ) {}
}
