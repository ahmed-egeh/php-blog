<?php
declare(strict_types=1);

use App\Domain\Post\Post;
use App\Http\AssetUrl;

/** @var Post $post */
$postId = $post->id->value;
$postImage = AssetUrl::postImage($post);
?>
<article id="post-<?= $postId ?>" class="blog-post">
    <header class="entry-header">
        <h1 class="entry-title">
            <a href="/posts/<?= $postId ?>" rel="bookmark">
                <?= htmlspecialchars($post->title->value, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h1>
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
            <?= htmlspecialchars($post->excerpt(), ENT_QUOTES, 'UTF-8') ?>
            <a class="more-link" href="/posts/<?= $postId ?>">Continue reading <span class="meta-nav">→</span></a>
        </p>
    </div>
</article>
