<?php
declare(strict_types=1);

namespace App\Application\User;

use App\Application\Port\AvatarStorage;
use App\Application\Port\CurrentUser;
use App\Application\Port\Mailer;
use App\Application\Port\RememberMe;
use App\Application\Port\UploadedImage;
use App\Application\Result;
use App\Domain\Shared\PersonName;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\Token;
use App\Domain\User\UserId;
use App\Domain\User\Username;
use App\Domain\User\UserRepository;
use Exception;

final class UserService
{
    public function __construct(
        private UserRepository $users,
        private Mailer $mail,
        private RememberMe $rememberMe,
        private AvatarStorage $avatars,
        private CurrentUser $currentUser,
    ) {}

    public function register(PersonName $firstName, PersonName $lastName, Password $password, Email $email): Result
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

        if (!$user || !$user->passwordMatches($password)) {
            return Result::fail('Invalid email or password.', 401);
        }

        if (!$user->canLogin()) {
            return Result::fail('Please activate your account before logging in.', 403);
        }

        $this->currentUser->login($user);

        if ($rememberMe) {
            $this->rememberMe->issue($user->id);
        } else {
            $this->rememberMe->clear($user->id);
        }

        return Result::ok('Logged in!');
    }

    public function logout(): void
    {
        $this->rememberMe->clear($this->currentUser->id());
        $this->currentUser->logout();
    }

    public function resumeRememberedSession(): void
    {
        if ($this->currentUser->isLoggedIn()) {
            return;
        }

        $plain = $this->rememberMe->consume();
        if ($plain === null || $plain === '') {
            return;
        }

        $user = $this->users->findByRememberToken(hash('sha256', $plain));
        if (!$user) {
            $this->rememberMe->clear(null);
            return;
        }

        $this->currentUser->login($user);
        $this->rememberMe->issue($user->id);
    }

    public function updateProfile(
        UserId $userId,
        PersonName $firstName,
        PersonName $lastName,
        ?UploadedImage $imageFile
    ): Result {
        $user = $this->users->find($userId);
        if (!$user) {
            return Result::fail('User not found.', 404);
        }

        $imagePath = $user->image;

        if ($imageFile !== null && !$imageFile->isMissing()) {
            $stored = $this->avatars->store($imageFile);
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

    public function changePassword(UserId $userId, Password $currentPassword, Password $newPassword): Result
    {
        $user = $this->users->find($userId);
        if (!$user || !$user->passwordMatches($currentPassword)) {
            return Result::fail('Current password is incorrect.', 401);
        }

        $this->users->updatePassword($userId, $newPassword->hash());

        return Result::ok('Password updated.');
    }

    public function deleteAccount(UserId $userId, Password $password): Result
    {
        $user = $this->users->find($userId);
        if (!$user || !$user->passwordMatches($password)) {
            return Result::fail('Password is incorrect.', 401);
        }

        $this->users->softDelete($userId);
        $this->logout();

        return Result::ok('Account deleted.');
    }
}
