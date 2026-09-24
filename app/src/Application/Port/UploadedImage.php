<?php
declare(strict_types=1);

namespace App\Application\Port;

final readonly class UploadedImage
{
    public function __construct(
        public string $tmpName,
        public int $error,
        public int $size,
    ) {}

    public function isMissing(): bool
    {
        return $this->error === UPLOAD_ERR_NO_FILE;
    }

    public function isOk(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }
}
