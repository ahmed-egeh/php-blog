<?php
declare(strict_types=1);

namespace App\Http;

final readonly class UploadedFile
{
    public function __construct(
        public string $tmpName,
        public int $error,
        public int $size,
    ) {}

    public static function fromRequest(string $key): ?self
    {
        if (!isset($_FILES[$key])) {
            return null;
        }

        $file = $_FILES[$key];

        return new self(
            tmpName: (string) ($file['tmp_name'] ?? ''),
            error: (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE),
            size: (int) ($file['size'] ?? 0),
        );
    }

    public function isMissing(): bool
    {
        return $this->error === UPLOAD_ERR_NO_FILE;
    }

    public function isOk(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }
}
