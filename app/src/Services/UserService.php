<?php
namespace App\Services;

use App\Models\User;
use Exception;

class UserService {
    public function create(string $firstName, string $lastName, string $password, string $email) {
        $userName = $firstName . '_' . $lastName . '_' . rand(1, 1000);
        [$token, $tokenHash, $expiresAt] = $this->generateToken();

        try {
            // save users
            $newUser = new User();
            $newUser->insertOne([
                "username" => $userName,
                "email" => $email,
                "password" => $password,
                "first_name" => $firstName,
                "last_name" => $lastName,
                "activated" => 0,
                "user_image" => NULL,
                "activation_token" => $tokenHash,
                "activation_expires_at" => $expiresAt,
            ]);
        } catch(Exception) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Could not create the account.',
            ]);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode(
            [ 'success' => true, 'message' => 'An Email has been sent!' ]
        );
    }
    private function generateToken(): array {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);

        return [$token, $tokenHash, $expiresAt];
    }
}