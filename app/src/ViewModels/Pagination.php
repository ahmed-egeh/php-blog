<?php
declare(strict_types=1);

namespace App\ViewModels;

final readonly class Pagination
{
    public function __construct(
        public int $page,
        public int $lastPage,
        public string $path = '/posts',
    ) {}

    public function url(int $page): string
    {
        if ($page <= 1) {
            return $this->path;
        }

        return $this->path . '?page=' . $page;
    }

    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->lastPage;
    }

    public function isCurrent(int $page): bool
    {
        return $this->page === $page;
    }

    public function shouldShow(): bool
    {
        return $this->lastPage > 1;
    }
}
