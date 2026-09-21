<nav class="navbar navbar-expand-lg">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse d-flex justify-content-center align-items-center" id="navbarNav">
    <ul class="navbar-nav d-flex justify-content-evenly align-items-center">
      <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/') ? 'active' : '' ?>">
        <a class="nav-link" href="/">Home</a>
      </li>

     <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/posts') ? 'active' : '' ?>"">
        <a class="nav-link" href="/posts">Posts</a>
      </li>

      <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/contact') ? 'active' : '' ?>"">
        <a class="nav-link" href="/contact">Contact</a>
      </li>

      <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/about') ? 'active' : '' ?>"">
        <a class="nav-link" href="/about">About</a>
      </li>

     <li class="nav-item <?= \App\Services\UtilService::isCurrentRoute('/login') || \App\Services\UtilService::isCurrentRoute('/signup') ? 'active' : '' ?>"">
        <a class="nav-link" href="/login">Login</a>
     </li>
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