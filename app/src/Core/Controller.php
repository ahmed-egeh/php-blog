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

    protected function json(bool $success, string $message, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function redirectIfLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
        }
    }
    
}