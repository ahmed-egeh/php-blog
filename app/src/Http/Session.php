<?php
declare(strict_types=1);

namespace App\Http;

use App\ValueObjects\InvalidValue;
use App\ValueObjects\UserId;

final class Session
{
    public static function hasUser(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?UserId
    {
        if (!self::hasUser()) {
            return null;
        }

        try {
            return new UserId((int) $_SESSION['user_id']);
        } catch (InvalidValue) {
            return null;
        }
    }

    public static function setUserId(UserId $userId): void
    {
        $_SESSION['user_id'] = $userId->value;
    }

    public static function flash(string $message): void
    {
        $_SESSION['flash'] = $message;
    }

    public static function pullFlash(): ?string
    {
        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_string($message) && $message !== '' ? $message : null;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
