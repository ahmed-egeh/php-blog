<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;

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

        // save users

        // send email

        #var_dump($userName, $firstName, $lastName, $email, $password, $passwordConfirmation);

        header('Content-Type: application/json');
        echo json_encode(
            [ 'success' => true, 'message' => 'User registered successfully' ]
        );
    }

}