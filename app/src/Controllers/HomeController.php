<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Services\PostService;
use App\ViewModels\HomePage;
use App\ViewModels\Page;

class HomeController extends Controller {
    public function index(): void {
        $this->view('home', new HomePage(
            title: 'Space Blog | Explore the universe',
            posts: (new PostService())->listHome(10),
        ));
    }

    public function about(): void {
        $this->view('about', new Page(
            title: 'About | Space Blog',
        ));
    }

    public function login(): void {
        $this->redirectIfLoggedIn();
        $this->view('login', new Page(
            title: 'Space Blog | Explore the universe',
        ));
    }

    public function signup(): void {
        $this->redirectIfLoggedIn();
        $this->view('signup', new Page(
            title: 'Space Blog | Explore the universe',
        ));
    }

}
