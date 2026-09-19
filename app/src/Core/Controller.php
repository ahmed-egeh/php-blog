<?php

namespace App\Core;

use Exception;

class Controller {
    protected function view(string $view, array $data = []): void
    {
        $path = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $path);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        $content = ob_get_clean();
        require dirname(__DIR__) . '/Views/layout.php';
    }
    
}