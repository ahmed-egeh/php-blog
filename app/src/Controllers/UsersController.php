<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Application\Port\CurrentUser;
use App\Application\Result;
use App\Application\User\UserService;
use App\Core\Controller;
use App\Domain\Shared\InvalidValue;
use App\Domain\Shared\PersonName;
use App\Domain\User\Email;
use App\Domain\User\Password;
use App\Domain\User\Token;
use App\Http\Request;
use App\Http\Response;

class UsersController extends Controller
{
    public function __construct(
        private UserService $users,
        CurrentUser $currentUser,
    ) {
        parent::__construct($currentUser);
    }

    public function signup(): Response
    {
        try {
            $firstName = new PersonName(Request::string('first_name'));
            $lastName = new PersonName(Request::string('last_name'));
            $email = new Email(Request::string('email'));
            $password = Password::fromNew(Request::string('password'));
            $passwordConfirmation = Password::fromNew(Request::string('password_confirmation'));
        } catch (InvalidValue $e) {
            return $this->json(Result::fail($e->getMessage(), 422));
        }

        if (!$password->matches($passwordConfirmation)) {
            return $this->json(Result::fail('Passwords do not match.', 422));
        }

        return $this->json($this->users->register($firstName, $lastName, $password, $email));
    }

    public function activate(): Response
    {
        $redirect = $this->redirectIfLoggedIn();
        if ($redirect !== null) {
            return $redirect;
        }

        try {
            $token = Token::fromPlain(Request::query('token'));
        } catch (InvalidValue) {
            return $this->text('Invalid activation link.', 400);
        }

        $result = $this->users->activate($token);

        return $this->text($result->message, $result->status);
    }

    public function login(): Response
    {
        try {
            $email = new Email(Request::string('email'));
            $password = Password::fromPlain(Request::string('password'));
        } catch (InvalidValue $e) {
            return $this->json(Result::fail($e->getMessage(), 422));
        }

        return $this->json($this->users->login(
            $email,
            $password,
            Request::has('rememberMe')
        ));
    }

    public function logout(): Response
    {
        $this->users->logout();

        return $this->redirect('/');
    }
}
