<?php
namespace App\Services;

use App\Models\User;

class AuthService {
    public static function loggedInUser(): array|null {
        if(isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->find((int) $_SESSION['user_id']);
            return $user;
        }
        return null;
    }
}