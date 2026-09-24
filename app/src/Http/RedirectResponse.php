<?php
declare(strict_types=1);

namespace App\Http;

final readonly class RedirectResponse implements Response
{
    public function __construct(
        public string $path,
        public int $status = 302,
    ) {}

    public function send(): void
    {
        http_response_code($this->status);
        header('Location: ' . $this->path);
    }
}
