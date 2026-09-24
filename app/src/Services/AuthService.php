<?php
declare(strict_types=1);

namespace App\Services;

use App\Http\Session;
use App\Repositories\UserRepository;
use App\ValueObjects\User;
use App\ValueObjects\UserId;

class AuthService
{
    public static function loggedInUserId(): ?UserId
    {
        return Session::userId();
    }

    public static function loggedInUser(): ?User
    {
        $userId = self::loggedInUserId();
        if ($userId === null) {
            return null;
        }

        return (new UserRepository())->find($userId);
    }

    public static function requireUserId(): UserId
    {
        $userId = self::loggedInUserId();
        if ($userId === null) {
            throw new \RuntimeException('Not authenticated.');
        }

        return $userId;
    }
}
