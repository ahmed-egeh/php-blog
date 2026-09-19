<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function get(
        string $path,
        mixed $handler
    ): void {
        $this->add('GET', $path, $handler);
    }

    public function post(
        string $path,
        mixed $handler
    ): void {
        $this->add('POST', $path, $handler);
    }

    private function add(
        string $method,
        string $path,
        mixed $handler
    ): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
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

        echo '404 Not Found';
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