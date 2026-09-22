<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    public static function loggedInUser(): array|null
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user = (new UserRepository())->find((int) $_SESSION['user_id']);

        return $user === false ? null : $user;
    }
}
