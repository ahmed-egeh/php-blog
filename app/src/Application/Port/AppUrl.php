<?php
declare(strict_types=1);

namespace App\Application\Port;

interface AppUrl
{
    public function to(string $path): string;
}
