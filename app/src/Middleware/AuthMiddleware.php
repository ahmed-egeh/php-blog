<?php
declare(strict_types=1);
namespace App\Middleware;

use Exception;

class AuthMiddleware {
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            throw new Exception('Unauthorized');
        }

        return true;
    }
}