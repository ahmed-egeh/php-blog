<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Coustard:wght@400;900&display=swap" rel="stylesheet">
    <title><?= htmlspecialchars($title ?? 'App', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/api.js"></script>
</head>
<style>
    body {
        font-family: "Coustard", Georgia, serif;
    }
    .blog-post {
        max-width: 760px;
        margin: 0 auto;
        padding: 1.25em 0 2.5em;
        color: #444;
        line-height: 1.6;
        text-align: left;
    }
    .blog-post p:last-child a {
        display: block;
        text-decoration: none;
        max-width: 10.75em;
        padding: 0.5em 0 0.4em 0;
        color: #444;
        font-weight: 400;
        font-size: .85em;
        background: none;
        border-bottom: 1px dotted #ccc;
        letter-spacing: 2px;
    }
    .blog-post .entry-title {
        font-size: 1.65rem;
        line-height: 1.3;
        margin: 0 0 0.4em;
        font-weight: 400;
        text-align: center;
    }
    .blog-post .entry-title a {
        color: #111;
        text-decoration: none;
    }
    .blog-post .entry-title a:hover {
        color: #cc3300;
    }
    .blog-post .entry-meta {
        font-size: 0.85rem;
        color: #888;
        margin-bottom: 1.1em;
        white-space: nowrap;
        text-align: center;
    }
    .blog-post .entry-meta span + span::before {
        content: " ";
    }
    .blog-post .by-author {
      font-size: 0.85rem;
    }
    .blog-post .by-author .author,
    .blog-post .entry-meta .post-date .entry-date,
    .blog-post .entry-meta .post-category .category {
        color: #333;
        border-bottom: 1px dotted #ccc;
    }
    .blog-post .featured-image {
        margin: 0 0 1.1em;
    }
    .blog-post .featured-image img {
        display: block;
        width: 100%;
        max-width: 760px;
        height: 220px;
        object-fit: contain;
        margin: 0 auto;
        background: #fff;
        border: 0;
    }
    .blog-post .entry-content {
        text-align: center;
    }
    .blog-post .entry-content p {
        margin-bottom: 0;
        text-align: left;
        font-weight: 200;
        font-family: sans-serif;
    }
    .blog-post .more-link {
        display: block;
        text-decoration: none;
        max-width: 18.75em;
        text-align: center;
        margin: 2.5em auto 0 auto;
        padding: 0.5em 0 0.4em 0;
        color: #444;
        font-weight: 400;
        font-size: .85em;
        background: none;
        border-bottom: 1px dotted #ccc;
        letter-spacing: 2px;
    }
    .all-posts-link {
        color: #cc3300;
        text-decoration: none;
    }
    .all-posts-link:hover {
        color: #cc3300;
        text-decoration: underline;
    }
    .avatar-sm,
    .avatar-md {
        object-fit: cover;
        background: #fff;
        border: 1px solid #111;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    .avatar-md {
        width: 96px;
        height: 96px;
    }
    .avatar-nav {
        width: 28px;
        height: 28px;
        object-fit: cover;
        background: #fff;
        border: 1px solid #111;
        border-radius: 50%;
    }
</style>
<body class="d-flex flex-column min-vh-100">
    <?php include("header.php"); ?>

    <main class="container py-4 flex-grow-1">
        <?php $flash = \App\Http\Session::pullFlash(); ?>
        <?php if ($flash !== null): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <?php require $viewPath; ?>
    </main>

    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>