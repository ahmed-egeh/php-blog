<?php
declare(strict_types=1);

namespace App\Http;

final readonly class TextResponse implements Response
{
    public function __construct(
        public string $body,
        public int $status = 200,
        public string $contentType = 'text/plain; charset=UTF-8',
    ) {}

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: ' . $this->contentType);
        echo $this->body;
    }
}
