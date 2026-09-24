<?php
use App\Services\UtilService;
use App\ViewModels\PostShowPage;

/** @var PostShowPage $page */
$post = $page->post;
$postId = $post->id->value;
$postImage = UtilService::postImageUrl($post);
?>
<article id="post-<?= $postId ?>" class="blog-post">
    <header class="entry-header">
        <h1 class="entry-title"><?= htmlspecialchars($post->title->value, ENT_QUOTES, 'UTF-8') ?></h1>
    </header>

    <footer class="entry-meta">
        <span class="post-category">
            posted in <?= htmlspecialchars($post->categoryName->value, ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="post-date">
            on
            <time class="entry-date" datetime="<?= htmlspecialchars($post->createdAt->format('c'), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($post->createdAt->format('F j, Y'), ENT_QUOTES, 'UTF-8') ?>
            </time>
        </span>
        <span class="by-author">
            by
            <span class="author"><?= htmlspecialchars($post->authorName(), ENT_QUOTES, 'UTF-8') ?></span>
        </span>
    </footer>

    <div class="featured-image">
        <img
            src="<?= htmlspecialchars($postImage, ENT_QUOTES, 'UTF-8') ?>"
            alt=""
            width="500"
            height="182"
        >
    </div>

    <div class="entry-content">
        <p style="white-space: pre-wrap;"><?= htmlspecialchars($post->content->value, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if ($page->isOwner): ?>
        <div class="d-flex gap-2 mt-4">
            <a href="/posts/<?= $postId ?>/edit" class="btn btn-outline-dark">Edit</a>
            <form action="/posts/<?= $postId ?>/delete" method="POST" onsubmit="return confirm('Delete this post?');">
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    <?php endif; ?>

    <p class="mt-4"><a href="/posts">← Back to posts</a></p>
</article>
