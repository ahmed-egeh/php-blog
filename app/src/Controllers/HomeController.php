<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index(): void {
        $this->view('home', [
            'title' => 'Space Blog | Explore the universe',
            'name' => 'Ahmed',
        ]);
    }

    public function login(): void {
        $this->view('login', [
            'title' => 'Space Blog | Explore the universe',
            'name' => 'Ahmed',
        ]);
    }
}