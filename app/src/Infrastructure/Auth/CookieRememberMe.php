<?php
declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Application\Port\RememberMe;
use App\Domain\User\Token;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use App\Http\Request;

final class CookieRememberMe implements RememberMe
{
    public function __construct(private UserRepository $users) {}

    public function issue(UserId $userId): void
    {
        $token = Token::generate();
        $this->users->updateRememberToken($userId, $token->hash);

        setcookie('remember_me', $token->plain, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => false,
        ]);
    }

    public function clear(?UserId $userId): void
    {
        if ($userId !== null) {
            $this->users->updateRememberToken($userId, null);
        }

        setcookie('remember_me', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public function consume(): ?string
    {
        $plain = Request::cookie('remember_me');

        return $plain === '' ? null : $plain;
    }
}
