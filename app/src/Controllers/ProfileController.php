<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Services\AuthService;
use App\Services\UserService;
use App\ValueObjects\InvalidValue;
use App\ValueObjects\Password;
use App\ValueObjects\PersonName;
use App\ViewModels\ProfilePage;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $users = new UserService(),
    ) {}

    public function show(): Response
    {
        $user = AuthService::loggedInUser();
        if (!$user) {
            return $this->redirect('/login');
        }

        return $this->view('profile/settings', new ProfilePage(
            title: 'Profile settings | Space Blog',
            user: $user,
        ));
    }

    public function update(): Response
    {
        try {
            $firstName = new PersonName(Request::string('first_name'));
            $lastName = new PersonName(Request::string('last_name'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/profile');
        }

        $result = $this->users->updateProfile(
            AuthService::requireUserId(),
            $firstName,
            $lastName,
            Request::file('user_image')
        );

        $this->flash($result->message);

        return $this->redirect('/profile');
    }

    public function updatePassword(): Response
    {
        try {
            $current = Password::fromPlain(Request::string('current_password'));
            $password = Password::fromNew(Request::string('password'));
            $confirmation = Password::fromNew(Request::string('password_confirmation'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/profile');
        }

        if (!$password->matches($confirmation)) {
            $this->flash('New passwords do not match.');
            return $this->redirect('/profile');
        }

        $result = $this->users->updatePassword(AuthService::requireUserId(), $current, $password);
        $this->flash($result->message);

        return $this->redirect('/profile');
    }

    public function destroy(): Response
    {
        try {
            $password = Password::fromPlain(Request::string('password'));
        } catch (InvalidValue $e) {
            $this->flash($e->getMessage());
            return $this->redirect('/profile');
        }

        $userId = AuthService::requireUserId();
        $result = $this->users->deleteAccount($userId, $password);

        if (!$result->success) {
            $this->flash($result->message);
            return $this->redirect('/profile');
        }

        $this->users->forgetRememberedLogin($userId);
        Session::destroy();

        return $this->redirect('/');
    }
}
