<?php
declare(strict_types=1);

namespace App\ViewModels;

abstract readonly class ViewModel
{
    public function __construct(
        public string $title,
    ) {}
}
