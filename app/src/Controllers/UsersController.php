<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Services\UserService;

class UsersController extends Controller {

    public function signup() {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        if(!$firstName || !$lastName || !$email || !$password || !$passwordConfirmation) {
            http_response_code(422);
            header('Content-Type: application/json');            
            echo json_encode(
                ['success' => false, 'message' => 'User registeration failed, missing some parameters!']
            );

            return;
        }

        if(strlen($password) < 8 || !preg_match('/\d/', $password) || !preg_match('/[a-zA-Z]/', $password)) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(
                [ 'success' => false, 'message' => 'Password needs to be above 8 characters with numbers and at least one characters' ]
            );
            return;
        }

        return (new UserService())->create($firstName, $lastName, $password, $email);
    }

    public function activate(): void
    {
        $this->redirectIfLoggedIn();
        $token = $_GET['token'] ?? null;

        if (!$token) {
            http_response_code(400);

            echo 'Invalid activation link.';
            return;
        }
        
        (new UserService())->activate($token);
    }

    public function login() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password) {
            http_response_code(422);
            header('Content-Type: application/json');            
            echo json_encode(
                ['success' => false, 'message' => 'User login failed, missing some parameters!']
            );

            return;
        }

        (new UserService())->login($email, $password);
    }

    public function logout(): void
    {
        // Remove all session data
        $_SESSION = [];

        // Remove the session cookie from the browser
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

        // Destroy server-side session
        session_destroy();

        // Redirect user
        header('Location: /');
        exit;
    }

}