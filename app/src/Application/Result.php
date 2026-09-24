<?php
declare(strict_types=1);

namespace App\Application;

use App\Domain\Post\PostId;

final readonly class Result implements \JsonSerializable
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $status = 200,
        public ?PostId $id = null,
    ) {}

    public function jsonSerialize(): object
    {
        $payload = new \stdClass();
        $payload->success = $this->success;
        $payload->message = $this->message;

        return $payload;
    }

    public static function ok(string $message, int $status = 200, ?PostId $id = null): self
    {
        return new self(true, $message, $status, $id);
    }

    public static function fail(string $message, int $status = 400): self
    {
        return new self(false, $message, $status);
    }
}
