<?php
declare(strict_types=1);

namespace App;

final readonly class Action
{
    public function __construct(
        public string $controller,
        public string $method,
    ) {}

    public function call(string ...$arguments): void
    {
        $controller = new $this->controller();
        $controller->{$this->method}(...$arguments);
    }
}
