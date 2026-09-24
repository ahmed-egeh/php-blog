<?php
declare(strict_types=1);

namespace App\ViewModels;

use App\Domain\User\User;

final readonly class ProfilePage extends ViewModel
{
    public function __construct(
        string $title,
        public User $user,
    ) {
        parent::__construct($title);
    }
}
