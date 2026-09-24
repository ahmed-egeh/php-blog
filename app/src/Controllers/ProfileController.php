<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\UserService;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $users = new UserService(),
    ) {}

    public function show(): void
    {
        $user = AuthService::loggedInUser();
        if (!$user) {
            $this->redirect('/login');
        }

        $this->view('profile/settings', [
            'title' => 'Profile settings | Space Blog',
            'user' => $user,
        ]);
    }

    public function update(): void
    {
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));

        if ($firstName === '' || $lastName === '') {
            $this->flash('First name and last name are required.');
            $this->redirect('/profile');
        }

        $result = $this->users->updateProfile(
            (int) $_SESSION['user_id'],
            $firstName,
            $lastName,
            $_FILES['user_image'] ?? null
        );

        $this->flash($result['message']);
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        $current = (string) ($_POST['current_password'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');

        if ($current === '' || $password === '' || $confirmation === '') {
            $this->flash('Fill in all password fields.');
            $this->redirect('/profile');
        }

        if ($password !== $confirmation) {
            $this->flash('New passwords do not match.');
            $this->redirect('/profile');
        }

        if (strlen($password) < 8 || !preg_match('/\d/', $password) || !preg_match('/[a-zA-Z]/', $password)) {
            $this->flash('New password must be at least 8 characters with a letter and a number.');
            $this->redirect('/profile');
        }

        $result = $this->users->updatePassword((int) $_SESSION['user_id'], $current, $password);
        $this->flash($result['message']);
        $this->redirect('/profile');
    }

    public function destroy(): void
    {
        $password = (string) ($_POST['password'] ?? '');

        if ($password === '') {
            $this->flash('Enter your password to delete the account.');
            $this->redirect('/profile');
        }

        $result = $this->users->deleteAccount((int) $_SESSION['user_id'], $password);

        if (!$result['success']) {
            $this->flash($result['message']);
            $this->redirect('/profile');
        }

        $this->users->forgetRememberedLogin((int) $_SESSION['user_id']);

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

        header('Location: /');
        exit;
    }
}
