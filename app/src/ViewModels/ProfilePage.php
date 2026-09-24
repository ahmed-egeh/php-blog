<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\ValueObjects\User;

final readonly class ProfilePage
{
    public function __construct(
        public string $title,
        public User $user,
    ) {}
}
