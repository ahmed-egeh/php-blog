<?php
declare(strict_types=1);

namespace App\Domain\Post;

use App\Domain\Shared\InvalidValue;

final readonly class PostContent
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidValue('Content is required.');
        }

        $this->value = $normalized;
    }

    public function excerpt(int $length = 180): string
    {
        if (strlen($this->value) <= $length) {
            return $this->value;
        }

        return substr($this->value, 0, $length) . '…';
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
