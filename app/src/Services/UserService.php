<?php
declare(strict_types=1);

namespace App\Services;

use App\Http\Request;
use App\Http\Session;
use App\Http\UploadedFile;
use App\Repositories\UserRepository;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use App\ValueObjects\PersonName;
use App\ValueObjects\Result;
use App\ValueObjects\Token;
use App\ValueObjects\UserId;
use App\ValueObjects\Username;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $users = new UserRepository(),
        private MailService $mail = new MailService(),
    ) {}

    public function create(PersonName $firstName, PersonName $lastName, Password $password, Email $email): Result
    {
        $username = Username::fromNames($firstName, $lastName);
        $token = Token::generate();
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);

        try {
            $this->users->create(
                $username,
                $email,
                $password->hash(),
                $firstName,
                $lastName,
                $token->hash,
                $expiresAt,
            );
        } catch (Exception) {
            return Result::fail('Could not create the account.', 500);
        }

        try {
            $link = 'http://localhost:8080/user/activate?token=' . urlencode($token->plain);
            $html = '<p>Click <a href="'
                . htmlspecialchars($link, ENT_QUOTES, 'UTF-8')
                . '">activate your account</a></p>';
            $this->mail->send($email, 'Activate your account', $html);
        } catch (Exception) {
            return Result::fail('Could not send the email!', 500);
        }

        return Result::ok('An Email has been sent!');
    }

    public function activate(Token $token): Result
    {
        $user = $this->users->findByActivationToken($token->hash);

        if (!$user) {
            return Result::fail('Invalid activation link.', 400);
        }

        if ($user->activationExpired()) {
            return Result::fail('Activation link has expired.', 400);
        }

        $this->users->activate($user->id);

        return Result::ok('Your account has been activated!');
    }

    public function login(Email $email, Password $password, bool $rememberMe = false): Result
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !$password->verify($user->password)) {
            return Result::fail('Invalid email or password.', 401);
        }

        if (!$user->activated) {
            return Result::fail('Please activate your account before logging in.', 403);
        }

        Session::regenerate();
        Session::setUserId($user->id);

        if ($rememberMe) {
            $this->issueRememberToken($user->id);
        } else {
            $this->clearRememberToken($user->id);
        }

        return Result::ok('Logged in!');
    }

    public function resumeRememberedSession(): void
    {
        if (Session::hasUser()) {
            return;
        }

        $token = Request::cookie('remember_me');
        if ($token === '') {
            return;
        }

        $user = $this->users->findByRememberToken(hash('sha256', $token));
        if (!$user) {
            $this->expireRememberCookie();
            return;
        }

        Session::regenerate();
        Session::setUserId($user->id);
        $this->issueRememberToken($user->id);
    }

    public function forgetRememberedLogin(?UserId $userId = null): void
    {
        if ($userId !== null) {
            $this->users->updateRememberToken($userId, null);
        }

        $this->expireRememberCookie();
    }

    private function issueRememberToken(UserId $userId): void
    {
        $token = Token::generate();
        $this->users->updateRememberToken($userId, $token->hash);

        setcookie('remember_me', $token->plain, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => false,
        ]);
    }

    private function clearRememberToken(UserId $userId): void
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
        UserId $userId,
        PersonName $firstName,
        PersonName $lastName,
        ?UploadedFile $imageFile
    ): Result {
        $user = $this->users->find($userId);
        if (!$user) {
            return Result::fail('User not found.', 404);
        }

        $imagePath = $user->image;

        if ($imageFile !== null && !$imageFile->isMissing()) {
            $stored = $this->storeAvatar($imageFile);
            if ($stored === null) {
                return Result::fail('Please upload a JPG, PNG, or WebP image under 2 MB.', 422);
            }
            $imagePath = $stored;
        }

        try {
            $this->users->updateProfile($userId, $firstName, $lastName, $imagePath);
        } catch (Exception) {
            return Result::fail('Could not update the profile.', 500);
        }

        return Result::ok('Profile saved.');
    }

    public function updatePassword(UserId $userId, Password $currentPassword, Password $newPassword): Result
    {
        $user = $this->users->find($userId);
        if (!$user || !$currentPassword->verify($user->password)) {
            return Result::fail('Current password is incorrect.', 401);
        }

        $this->users->updatePassword($userId, $newPassword->hash());

        return Result::ok('Password updated.');
    }

    public function deleteAccount(UserId $userId, Password $password): Result
    {
        $user = $this->users->find($userId);
        if (!$user || !$password->verify($user->password)) {
            return Result::fail('Password is incorrect.', 401);
        }

        $this->users->softDelete($userId);

        return Result::ok('Account deleted.');
    }

    private function storeAvatar(UploadedFile $file): ?string
    {
        if (!$file->isOk()) {
            return null;
        }

        if ($file->size > 2 * 1024 * 1024) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->tmpName);
        if (!is_string($mime)) {
            return null;
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };

        if ($extension === null) {
            return null;
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads/avatars';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return null;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($file->tmpName, $destination)) {
            return null;
        }

        return 'uploads/avatars/' . $filename;
    }
}
