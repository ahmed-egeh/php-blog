<?php
declare(strict_types=1);

namespace App\Persistence;

final readonly class UserInsert
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $first_name,
        public string $last_name,
        public int $activated,
        public ?string $user_image,
        public string $activation_token,
        public string $activation_expires_at,
    ) {}
}
