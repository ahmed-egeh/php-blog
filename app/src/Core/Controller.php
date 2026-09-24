<?php

namespace App\Core;

use App\Http\Session;
use App\ValueObjects\Result;

class Controller {
    protected function view(string $view, object $page): void
    {
        $path = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $path);
        }
        $title = $page->title ?? 'App';
        ob_start();
        require $path;
        $content = ob_get_clean();
        require dirname(__DIR__) . '/Views/layout.php';
    }

    protected function json(Result $result): void
    {
        http_response_code($result->status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function flash(string $message): void
    {
        Session::flash($message);
    }

    public function redirectIfLoggedIn() {
        if (Session::hasUser()) {
            $this->redirect('/');
        }
    }
    
}
