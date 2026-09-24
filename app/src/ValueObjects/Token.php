<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Token
{
    public function __construct(
        public string $plain,
        public string $hash,
    ) {
        if ($this->plain === '' || $this->hash === '') {
            throw new InvalidValue('Token is required.');
        }
    }

    public static function generate(): self
    {
        $plain = bin2hex(random_bytes(32));

        return new self($plain, hash('sha256', $plain));
    }

    public static function fromPlain(string $plain): self
    {
        $plain = trim($plain);

        if ($plain === '') {
            throw new InvalidValue('Token is required.');
        }

        return new self($plain, hash('sha256', $plain));
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->hash, $other->hash);
    }
}
