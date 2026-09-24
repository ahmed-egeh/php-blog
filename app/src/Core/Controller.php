<?php
declare(strict_types=1);

namespace App\Core;

use App\Application\Port\CurrentUser;
use App\Application\Result;
use App\Http\JsonResponse;
use App\Http\RedirectResponse;
use App\Http\Session;
use App\Http\TextResponse;
use App\Http\ViewResponse;
use App\ViewModels\ViewModel;

class Controller
{
    public function __construct(
        protected CurrentUser $currentUser,
    ) {}

    protected function view(string $view, ViewModel $page): ViewResponse
    {
        return new ViewResponse($view, $page, $this->currentUser->user());
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
        return $this->currentUser->isLoggedIn() ? $this->redirect('/') : null;
    }
}
