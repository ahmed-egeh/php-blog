<?php
declare(strict_types=1);

namespace App;

final readonly class MiddlewareQueue
{
    /** @var list<string> */
    private array $classes;

    public function __construct(string ...$classes)
    {
        $this->classes = $classes;
    }

    public function run(): void
    {
        foreach ($this->classes as $class) {
            (new $class())->handle();
        }
    }
}
