<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Application\Port\CurrentUser;
use App\Application\Post\PostService;
use App\Core\Controller;
use App\Http\Response;
use App\ViewModels\HomePage;
use App\ViewModels\Page;

class HomeController extends Controller
{
    public function __construct(
        private PostService $posts,
        CurrentUser $currentUser,
    ) {
        parent::__construct($currentUser);
    }

    public function index(): Response
    {
        return $this->view('home', new HomePage(
            title: 'Space Blog | Explore the universe',
            posts: $this->posts->listHome(10),
        ));
    }

    public function about(): Response
    {
        return $this->view('about', new Page(
            title: 'About | Space Blog',
        ));
    }

    public function login(): Response
    {
        return $this->redirectIfLoggedIn() ?? $this->view('login', new Page(
            title: 'Space Blog | Explore the universe',
        ));
    }

    public function signup(): Response
    {
        return $this->redirectIfLoggedIn() ?? $this->view('signup', new Page(
            title: 'Space Blog | Explore the universe',
        ));
    }
}
