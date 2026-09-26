<?php
declare(strict_types=1);

namespace App\ViewModels;

final readonly class ResetPasswordPage extends ViewModel
{
    public function __construct(
        string $title,
        public string $token,
    ) {
        parent::__construct($title);
    }
}
