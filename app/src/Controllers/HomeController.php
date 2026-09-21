<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class HomeController extends Controller {
    public function index(): void {
        $this->view('home', [
            'title' => 'Space Blog | Explore the universe',
            'name' => AuthService::loggedInUser()['username'] ?? 'User',
        ]);
    }

    public function login(): void {
        $this->view('login', [
            'title' => 'Space Blog | Explore the universe',
            'name' => AuthService::loggedInUser()['username'] ?? 'User',
        ]);
    }

    // signup
    public function signup(): void {
        $this->view('signup', [
            'title' => 'Space Blog | Explore the universe',
            'name' => AuthService::loggedInUser()['username'] ?? 'User',
        ]);
    }

}