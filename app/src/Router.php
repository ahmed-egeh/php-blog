<?php

declare(strict_types=1);

namespace App;

use Exception;

class Router
{
    private array $routes = [];

    public function get(
        string $path,
        mixed $handler,
        array $middlewares = []
    ): void {
        $this->add('GET', $path, $handler, $middlewares);
    }

    public function post(
        string $path,
        mixed $handler,
        array $middlewares = []   
    ): void {
        $this->add('POST', $path, $handler, $middlewares);
    }

    private function add(
        string $method,
        string $path,
        mixed $handler,
        array $middlewares = []
    ): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(
        string $method,
        string $uri
    ): void {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = preg_replace(
                '#\{([^/]+)\}#',
                '([^/]+)',
                $route['path']
            );

            $regex = '#^' . $regex . '$#';

            if (!preg_match($regex, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach ($route['middlewares'] as $middleware) {
                (new $middleware())->handle();
            }

            $handler = $this->resolveHandler(
                $route['handler']
            );

            call_user_func_array(
                $handler,
                $matches
            );

            return;
        }

        http_response_code(404);

        throw new Exception("Route not found!");
    }

    private function resolveHandler(
        mixed $handler
    ): callable {
        if (
            is_array($handler)
            && isset($handler[0], $handler[1])
            && is_string($handler[0])
        ) {
            [$controllerClass, $method] = $handler;

            $controller = new $controllerClass();

            return [$controller, $method];
        }

        if (is_callable($handler)) {
            return $handler;
        }

        throw new \RuntimeException(
            'Invalid route handler'
        );
    }
}