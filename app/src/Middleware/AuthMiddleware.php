<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Application\Port\CurrentUser;
use App\Http\RedirectResponse;
use App\Http\Response;

final class AuthMiddleware
{
    public function __construct(private CurrentUser $currentUser) {}

    public function handle(): ?Response
    {
        if ($this->currentUser->isLoggedIn()) {
            return null;
        }

        return new RedirectResponse('/login');
    }
}
