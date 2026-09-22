<?php
namespace App\Services;

use App\Models\User;
use Exception;

class UserService {
    public function create(string $firstName, string $lastName, string $password, string $email): array {
        $userName = $firstName . '_' . $lastName . '_' . rand(1, 1000);
        [$token, $tokenHash, $expiresAt] = $this->generateToken();

        try {
            $newUser = new User();
            $newUser->insertOne([
                "username" => $userName,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT),
                "first_name" => $firstName,
                "last_name" => $lastName,
                "activated" => 0,
                "user_image" => NULL,
                "activation_token" => $tokenHash,
                "activation_expires_at" => $expiresAt,
            ]);
        } catch(Exception) {
            return $this->result(false, 'Could not create the account.', 500);
        }

        try {
            $link = 'http://localhost:8080/user/activate?token=' . urlencode($token);
            $html = '<p>Click <a href="'
                . htmlspecialchars($link, ENT_QUOTES, 'UTF-8')
                . '">activate your account</a></p>';
            (new MailService())->send($email, 'Activate your account', $html);
        } catch(Exception) {
            return $this->result(false, 'Could not send the email!', 500);
        }

        return $this->result(true, 'An Email has been sent!');
    }

    private function generateToken(): array {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);

        return [$token, $tokenHash, $expiresAt];
    }

    public function activate(string $token) {
        $tokenHash = hash('sha256', $token);

        $userModel = new User();

        $user = $userModel->findByActivationToken($tokenHash);

        if (!$user) {
            http_response_code(400);

            echo 'Invalid activation link.';
            return;
        }

        if (strtotime($user['activation_expires_at']) < time()) {
            http_response_code(400);

            echo 'Activation link has expired.';
            return;
        }

        $userModel->activate($user['id']);

        echo 'Your account has been activated!';
    }

    public function login(string $email, string $password): array {
        $userModel = new User();

        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->result(false, 'Invalid email or password.', 401);
        }

        if (!$user['activated']) {
            return $this->result(false, 'Please activate your account before logging in.', 403);
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];

        return $this->result(true, 'Logged in!');
    }

    private function result(bool $success, string $message, int $status = 200): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status,
        ];
    }
}
