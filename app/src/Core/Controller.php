<?php
declare(strict_types=1);

namespace App\Core;

use App\Http\JsonResponse;
use App\Http\RedirectResponse;
use App\Http\Session;
use App\Http\TextResponse;
use App\Http\ViewResponse;
use App\ValueObjects\Result;
use App\ViewModels\ViewModel;

class Controller
{
    protected function view(string $view, ViewModel $page): ViewResponse
    {
        return new ViewResponse($view, $page);
    }

    protected function json(Result $result): JsonResponse
    {
        return new JsonResponse($result);
    }

    protected function redirect(string $path): RedirectResponse
    {
        return new RedirectResponse($path);
    }

    protected function text(string $body, int $status = 200): TextResponse
    {
        return new TextResponse($body, $status);
    }

    protected function flash(string $message): void
    {
        Session::flash($message);
    }

    protected function redirectIfLoggedIn(): ?RedirectResponse
    {
        return Session::hasUser() ? $this->redirect('/') : null;
    }
}
