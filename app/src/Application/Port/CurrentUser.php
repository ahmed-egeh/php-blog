<?php
declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\User\User;
use App\Domain\User\UserId;

interface CurrentUser
{
    public function id(): ?UserId;

    public function user(): ?User;

    public function requireId(): UserId;

    public function login(User $user): void;

    public function logout(): void;

    public function isLoggedIn(): bool;
}
