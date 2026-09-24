<?php

use App\Services\AuthService;
?>
<nav class="navbar navbar-expand-lg">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse d-flex justify-content-center align-items-center" id="navbarNav">
    <ul class="navbar-nav d-flex justify-content-evenly align-items-center">
      <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/') ? 'active' : '' ?>">
        <a class="nav-link" href="/">Home</a>
      </li>

     <li class="nav-item <?= str_starts_with((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/posts') ? 'active' : '' ?>">
        <a class="nav-link" href="/posts">Posts</a>
      </li>

      <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/about') ? 'active' : '' ?>"">
        <a class="nav-link" href="/about">About</a>
      </li>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/login') || \App\Services\UtilService::isCurrentRoute('/signup') ? 'active' : '' ?>"">
            <a class="nav-link" href="/login">Login</a>
        </li>

    <?php else: ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php $user = AuthService::loggedInUser(); ?>
            <img
              src="<?= htmlspecialchars(\App\Services\UtilService::avatarUrl($user['user_image'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
              alt=""
              class="avatar-nav"
            >
            Hi <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="/profile">My Profile</a></li>
            <li><a class="dropdown-item" href="/posts/mine">My posts</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/user/logout">Logout</a></li>
          </ul>
        </li>

    <?php endif; ?>


    </ul>
  </div>
</nav>

<style>
    nav {
        border: 1px solid #eee;
    }
    .navbar-nav {
        width: 30%;
    }
    .navbar-nav > li:hover:not(.nav-item.active) {
        border-bottom: 1px solid #aaa;
    }
    .navbar-nav > .nav-item.active > .nav-link {
        color: #cc3300;
        border-bottom: 1px solid #cc3300;
    }
</style>