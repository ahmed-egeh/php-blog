<?php
declare(strict_types=1);

namespace App\Http;

use App\Application\Result;

final readonly class JsonResponse implements Response
{
    public function __construct(
        public Result $result,
    ) {}

    public function send(): void
    {
        http_response_code($this->result->status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($this->result, JSON_THROW_ON_ERROR);
    }
}
