<?php
declare(strict_types=1);

namespace App;

use App\Http\Response;
use App\Infrastructure\Container;

final readonly class MiddlewareQueue
{
    /** @var list<string> */
    private array $classes;

    public function __construct(string ...$classes)
    {
        $this->classes = $classes;
    }

    public function run(Container $container): ?Response
    {
        foreach ($this->classes as $class) {
            $middleware = $container->get($class);
            $result = $middleware->handle();
            if ($result instanceof Response) {
                return $result;
            }
        }

        return null;
    }
}
