<?php

declare(strict_types=1);

namespace App;

use App\Http\TextResponse;

class Router
{
    public function __construct(
        private RouteList $routes = new RouteList(),
    ) {}

    public function get(
        string $path,
        string $controller,
        string $action,
        string ...$middlewares
    ): void {
        $this->add('GET', $path, $controller, $action, ...$middlewares);
    }

    public function post(
        string $path,
        string $controller,
        string $action,
        string ...$middlewares
    ): void {
        $this->add('POST', $path, $controller, $action, ...$middlewares);
    }

    private function add(
        string $method,
        string $path,
        string $controller,
        string $action,
        string ...$middlewares
    ): void {
        $this->routes->add(new Route(
            $method,
            $path,
            new Action($controller, $action),
            new MiddlewareQueue(...$middlewares),
        ));
    }

    public function dispatch(
        string $method,
        string $uri
    ): void {
        foreach ($this->routes as $route) {
            if ($route->method !== $method) {
                continue;
            }

            $pattern = preg_replace(
                '#\{([^/]+)\}#',
                '([^/]+)',
                $route->path
            );

            if (!is_string($pattern)) {
                continue;
            }

            $regex = '#^' . $pattern . '$#';

            if (!preg_match($regex, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            $arguments = [];
            foreach ($matches as $match) {
                $arguments[] = (string) $match;
            }

            $route->middlewares->run();
            $route->action->call(...$arguments)->send();

            return;
        }

        (new TextResponse('Route not found!', 404))->send();
    }
}
