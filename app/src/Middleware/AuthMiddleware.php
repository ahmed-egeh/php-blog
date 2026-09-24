<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Http\Session;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (!Session::hasUser()) {
            header('Location: /login');
            exit;
        }

        return true;
    }
}
