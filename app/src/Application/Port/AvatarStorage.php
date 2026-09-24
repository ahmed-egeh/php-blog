<?php
declare(strict_types=1);

namespace App\Application\Port;

interface AvatarStorage
{
    public function store(UploadedImage $file): ?string;
}
