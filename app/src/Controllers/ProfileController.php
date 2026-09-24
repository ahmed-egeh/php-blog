<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Application\Port\CurrentUser;
use App\Application\User\UserService;
use App\Core\Controller;
use App\Domain\Shared\InvalidValue;
use App\Domain\Shared\PersonName;
use App\Domain\User\Password;
use App\Http\Request;
use App\Http\Response;
use App\ViewModels\ProfilePage;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $users,
        CurrentUser $currentUser,
    ) {
        parent::__construct($currentUser);
    }

    public function show(): Response
    {
        $user = $this->currentUser->user();
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
            $this->currentUser->requireId(),
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

        $result = $this->users->changePassword($this->currentUser->requireId(), $current, $password);
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

        $result = $this->users->deleteAccount($this->currentUser->requireId(), $password);

        if (!$result->success) {
            $this->flash($result->message);
            return $this->redirect('/profile');
        }

        return $this->redirect('/');
    }
}
