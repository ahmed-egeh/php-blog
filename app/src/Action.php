<?php
declare(strict_types=1);

namespace App;

use App\Http\Response;

final readonly class Action
{
    public function __construct(
        public string $controller,
        public string $method,
    ) {}

    public function call(string ...$arguments): Response
    {
        $controller = new $this->controller();
        $response = $controller->{$this->method}(...$arguments);

        if (!$response instanceof Response) {
            throw new \RuntimeException(
                $this->controller . '::' . $this->method . ' must return a Response.'
            );
        }

        return $response;
    }
}
