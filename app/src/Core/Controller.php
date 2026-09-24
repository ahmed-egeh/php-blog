<?php
declare(strict_types=1);

namespace App\Core;

use App\Http\Session;
use App\ValueObjects\Result;
use App\ViewModels\ViewModel;

class Controller
{
    protected function view(string $view, ViewModel $page): void
    {
        $path = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $path);
        }

        $title = $page->title;
        ob_start();
        require $path;
        $content = ob_get_clean();
        if ($content === false) {
            $content = '';
        }

        require dirname(__DIR__) . '/Views/layout.php';
    }

    protected function json(Result $result): void
    {
        http_response_code($result->status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result, JSON_THROW_ON_ERROR);
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

    public function redirectIfLoggedIn(): void
    {
        if (Session::hasUser()) {
            $this->redirect('/');
        }
    }
}
