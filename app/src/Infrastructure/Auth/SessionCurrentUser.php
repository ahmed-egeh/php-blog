<?php
declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Application\Port\CurrentUser;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use App\Http\Session;
use RuntimeException;

final class SessionCurrentUser implements CurrentUser
{
    private ?User $resolved = null;

    private bool $loaded = false;

    public function __construct(private UserRepository $users) {}

    public function id(): ?UserId
    {
        return Session::userId();
    }

    public function user(): ?User
    {
        if ($this->loaded) {
            return $this->resolved;
        }

        $this->loaded = true;
        $id = $this->id();
        $this->resolved = $id === null ? null : $this->users->find($id);

        return $this->resolved;
    }

    public function requireId(): UserId
    {
        $id = $this->id();
        if ($id === null) {
            throw new RuntimeException('Not authenticated.');
        }

        return $id;
    }

    public function login(User $user): void
    {
        Session::regenerate();
        Session::setUserId($user->id);
        $this->resolved = $user;
        $this->loaded = true;
    }

    public function logout(): void
    {
        Session::destroy();
        $this->resolved = null;
        $this->loaded = true;
    }

    public function isLoggedIn(): bool
    {
        return $this->id() !== null;
    }
}
