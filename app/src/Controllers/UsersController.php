<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\UserService;
use Exception;

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
        $token = $_GET['token'] ?? null;

        if (!$token) {
            http_response_code(400);

            echo 'Invalid activation link.';
            return;
        }

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

}