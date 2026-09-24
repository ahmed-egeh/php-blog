<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session;
use App\Services\AuthService;
use App\Services\UserService;
use App\ValueObjects\Email;
use App\ValueObjects\InvalidValue;
use App\ValueObjects\Password;
use App\ValueObjects\PersonName;
use App\ValueObjects\Result;
use App\ValueObjects\Token;

class UsersController extends Controller {

    public function signup(): Response {
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

        return $this->json((new UserService())->create($firstName, $lastName, $password, $email));
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

        $result = (new UserService())->activate($token);

        return $this->text($result->message, $result->status);
    }

    public function login(): Response {
        try {
            $email = new Email(Request::string('email'));
            $password = Password::fromPlain(Request::string('password'));
        } catch (InvalidValue $e) {
            return $this->json(Result::fail($e->getMessage(), 422));
        }

        return $this->json((new UserService())->login(
            $email,
            $password,
            Request::has('rememberMe')
        ));
    }

    public function logout(): Response
    {
        (new UserService())->forgetRememberedLogin(AuthService::loggedInUserId());
        Session::destroy();

        return $this->redirect('/');
    }

}
