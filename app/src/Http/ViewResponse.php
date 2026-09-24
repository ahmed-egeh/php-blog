<?php
declare(strict_types=1);

namespace App\Http;

use App\Domain\User\User;
use App\ViewModels\ViewModel;

final readonly class ViewResponse implements Response
{
    public function __construct(
        public string $view,
        public ViewModel $page,
        public ?User $currentUser = null,
        public int $status = 200,
    ) {}

    public function send(): void
    {
        $viewPath = dirname(__DIR__) . '/Views/' . $this->view . '.php';
        if (!is_file($viewPath)) {
            throw new \RuntimeException('View not found: ' . $viewPath);
        }

        http_response_code($this->status);

        $page = $this->page;
        $title = $page->title;
        $currentUser = $this->currentUser;

        require dirname(__DIR__) . '/Views/layout.php';
    }
}
