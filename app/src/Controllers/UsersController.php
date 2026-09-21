<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
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

        $userName = $firstName . '_' . $lastName . '_' . rand(1, 1000);

        try {
            // save users
            $newUser = new User();
            $newUser->insertOne([
                "username" => $userName,
                "email" => $email,
                "password" => $password,
                "first_name" => $firstName,
                "last_name" => $lastName,
                "activated" => 0,
                "user_image" => NULL,
            ]);
        } catch(Exception) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Could not create the account.',
            ]);
            return;
        }

        // send email

        #var_dump($userName, $firstName, $lastName, $email, $password, $passwordConfirmation);

        header('Content-Type: application/json');
        echo json_encode(
            [ 'success' => true, 'message' => 'An Email has been sent!' ]
        );
    }

}