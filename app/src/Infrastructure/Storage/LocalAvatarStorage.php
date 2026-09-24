<?php
declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Port\AvatarStorage;
use App\Application\Port\UploadedImage;

final class LocalAvatarStorage implements AvatarStorage
{
    public function store(UploadedImage $file): ?string
    {
        if (!$file->isOk()) {
            return null;
        }

        if ($file->size > 2 * 1024 * 1024) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->tmpName);
        if (!is_string($mime)) {
            return null;
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };

        if ($extension === null) {
            return null;
        }

        $directory = dirname(__DIR__, 3) . '/public/uploads/avatars';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return null;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($file->tmpName, $destination)) {
            return null;
        }

        return 'uploads/avatars/' . $filename;
    }
}
