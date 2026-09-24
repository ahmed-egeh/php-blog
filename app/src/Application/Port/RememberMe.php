<?php
declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\User\UserId;

interface RememberMe
{
    public function issue(UserId $userId): void;

    public function clear(?UserId $userId): void;

    public function consume(): ?string;
}
