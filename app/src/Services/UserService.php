<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $users = new UserRepository(),
        private MailService $mail = new MailService(),
    ) {}

    public function create(string $firstName, string $lastName, string $password, string $email): array
    {
        $userName = $firstName . '_' . $lastName . '_' . rand(1, 1000);
        [$token, $tokenHash, $expiresAt] = $this->generateToken();

        try {
            $this->users->create([
                'username' => $userName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'activated' => 0,
                'user_image' => null,
                'activation_token' => $tokenHash,
                'activation_expires_at' => $expiresAt,
            ]);
        } catch (Exception) {
            return $this->result(false, 'Could not create the account.', 500);
        }

        try {
            $link = 'http://localhost:8080/user/activate?token=' . urlencode($token);
            $html = '<p>Click <a href="'
                . htmlspecialchars($link, ENT_QUOTES, 'UTF-8')
                . '">activate your account</a></p>';
            $this->mail->send($email, 'Activate your account', $html);
        } catch (Exception) {
            return $this->result(false, 'Could not send the email!', 500);
        }

        return $this->result(true, 'An Email has been sent!');
    }

    public function activate(string $token): void
    {
        $tokenHash = hash('sha256', $token);
        $user = $this->users->findByActivationToken($tokenHash);

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

        $this->users->activate((int) $user['id']);

        echo 'Your account has been activated!';
    }

    public function login(string $email, string $password, bool $rememberMe = false): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->result(false, 'Invalid email or password.', 401);
        }

        if (!$user['activated']) {
            return $this->result(false, 'Please activate your account before logging in.', 403);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        if ($rememberMe) {
            $this->issueRememberToken((int) $user['id']);
        } else {
            $this->clearRememberToken((int) $user['id']);
        }

        return $this->result(true, 'Logged in!');
    }

    public function resumeRememberedSession(): void
    {
        if (isset($_SESSION['user_id'])) {
            return;
        }

        $token = (string) ($_COOKIE['remember_me'] ?? '');
        if ($token === '') {
            return;
        }

        $user = $this->users->findByRememberToken(hash('sha256', $token));
        if (!$user) {
            $this->expireRememberCookie();
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $this->issueRememberToken((int) $user['id']);
    }

    public function forgetRememberedLogin(?int $userId = null): void
    {
        if ($userId !== null) {
            $this->users->updateRememberToken($userId, null);
        }

        $this->expireRememberCookie();
    }

    private function issueRememberToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $this->users->updateRememberToken($userId, hash('sha256', $token));

        setcookie('remember_me', $token, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => false,
        ]);
    }

    private function clearRememberToken(int $userId): void
    {
        $this->users->updateRememberToken($userId, null);
        $this->expireRememberCookie();
    }

    private function expireRememberCookie(): void
    {
        setcookie('remember_me', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public function updateProfile(
        int $userId,
        string $firstName,
        string $lastName,
        ?array $imageFile
    ): array {
        $user = $this->users->find($userId);
        if (!$user) {
            return $this->result(false, 'User not found.', 404);
        }

        $imagePath = $user['user_image'];

        if ($imageFile !== null && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $stored = $this->storeAvatar($imageFile);
            if ($stored === null) {
                return $this->result(false, 'Please upload a JPG, PNG, or WebP image under 2 MB.', 422);
            }
            $imagePath = $stored;
        }

        try {
            $this->users->updateProfile($userId, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_image' => $imagePath,
            ]);
        } catch (Exception) {
            return $this->result(false, 'Could not update the profile.', 500);
        }

        return $this->result(true, 'Profile saved.');
    }

    public function updatePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->users->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return $this->result(false, 'Current password is incorrect.', 401);
        }

        $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        return $this->result(true, 'Password updated.');
    }

    public function deleteAccount(int $userId, string $password): array
    {
        $user = $this->users->find($userId);
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->result(false, 'Password is incorrect.', 401);
        }

        $this->users->softDelete($userId);

        return $this->result(true, 'Account deleted.');
    }

    private function storeAvatar(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($extensions[$mime])) {
            return null;
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads/avatars';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return null;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return null;
        }

        return 'uploads/avatars/' . $filename;
    }

    private function generateToken(): array
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);

        return [$token, $tokenHash, $expiresAt];
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
