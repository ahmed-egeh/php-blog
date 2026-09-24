<?php
declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\User\Email;

interface Mailer
{
    public function send(Email $to, string $subject, string $html): void;
}
