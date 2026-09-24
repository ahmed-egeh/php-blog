<?php
declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\Email;

class MailService
{
    public function send(Email $to, string $subject, string $html): void
    {
        $host = getenv('MAIL_HOST') ?: 'mailpit';
        $port = (int) (getenv('MAIL_PORT') ?: 1025);
        $from = getenv('MAIL_FROM') ?: 'noreply@mars.local';

        $to = $this->headerSafe($to->value);
        $from = $this->headerSafe($from);
        $subject = $this->headerSafe($subject);

        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($socket === false) {
            throw new \RuntimeException('SMTP connect failed: ' . $errstr);
        }

        $this->expect($socket, 220);
        $this->command($socket, 'EHLO mars.local', 250);
        $this->command($socket, 'MAIL FROM:<' . $from . '>', 250);
        $this->command($socket, 'RCPT TO:<' . $to . '>', 250);
        $this->command($socket, 'DATA', 354);

        $message =
            "From: {$from}\r\n" .
            "To: {$to}\r\n" .
            "Subject: {$subject}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/html; charset=UTF-8\r\n" .
            "\r\n" .
            $html .
            "\r\n.\r\n";

        fwrite($socket, $message);
        $this->expect($socket, 250);
        $this->command($socket, 'QUIT', 221);
        fclose($socket);
    }

    private function command($socket, string $line, int $code): void
    {
        fwrite($socket, $line . "\r\n");
        $this->expect($socket, $code);
    }

    private function expect($socket, int $code): void
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if (!str_starts_with($response, (string) $code)) {
            throw new \RuntimeException('SMTP error: ' . trim($response));
        }
    }

    private function headerSafe(string $value): string
    {
        return str_replace(["\r", "\n"], '', $value);
    }
}