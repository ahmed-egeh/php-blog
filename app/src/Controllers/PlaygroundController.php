<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Http\Response;

class PlaygroundController extends Controller {
    public function index(): Response {
        return $this->text('');
    }
}
