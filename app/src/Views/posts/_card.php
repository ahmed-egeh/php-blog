<?php
use App\Services\UtilService;

$postId = (int) $post['id'];
$postImage = UtilService::postImageUrl($post['image'] ?? null, $postId);
$created = strtotime((string) $post['created_at']) ?: time();
$excerpt = strlen($post['content']) > 180
    ? substr($post['content'], 0, 180) . '…'
    : $post['content'];
$authorName = trim($post['first_name'] . ' ' . $post['last_name']);
?>
<article id="post-<?= $postId ?>" class="blog-post">
    <header class="entry-header">
        <h1 class="entry-title">
            <a href="/posts/<?= $postId ?>" rel="bookmark">
                <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h1>
    </header>

    <footer class="entry-meta">
        <span class="post-category">
            posted in <?= htmlspecialchars($post['category_name'], ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="post-date">
            on
            <time class="entry-date" datetime="<?= htmlspecialchars(date('c', $created), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(date('F j, Y', $created), ENT_QUOTES, 'UTF-8') ?>
            </time>
        </span>
        <span class="by-author">
            by
            <span class="author"><?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?></span>
        </span>
    </footer>

    <div class="featured-image">
        <a href="/posts/<?= $postId ?>">
            <img
                src="<?= htmlspecialchars($postImage, ENT_QUOTES, 'UTF-8') ?>"
                alt=""
                width="500"
                height="182"
            >
        </a>
    </div>

    <div class="entry-content">
        <p>
            <?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?>
            <a class="more-link" href="/posts/<?= $postId ?>">Continue reading <span class="meta-nav">→</span></a>
        </p>
    </div>
</article>
