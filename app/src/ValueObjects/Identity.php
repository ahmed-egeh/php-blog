<?php
declare(strict_types=1);

namespace App\ValueObjects;

abstract readonly class Identity
{
    public int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidValue('Invalid id.');
        }

        $this->value = $value;
    }

    public function equals(self $other): bool
    {
        return $this::class === $other::class && $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
