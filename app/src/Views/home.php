<?php
$hasLikes = false;
foreach ($posts as $post) {
    if ((int) ($post['likes_count'] ?? 0) > 0) {
        $hasLikes = true;
        break;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $hasLikes ? 'Top liked posts' : 'Latest posts' ?></h1>
    <a href="/posts">All posts</a>
</div>

<?php if ($posts === []): ?>
    <p class="text-muted">No posts yet.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="mb-4 pb-3 border-bottom">
            <h2 class="h4">
                <a href="/posts/<?= (int) $post['id'] ?>" class="text-decoration-none" style="color: #cc3300;">
                    <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>
            <p class="text-muted small mb-2">
                <?= htmlspecialchars($post['category_name'], ENT_QUOTES, 'UTF-8') ?>
                ·
                <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name'], ENT_QUOTES, 'UTF-8') ?>
                ·
                <?= htmlspecialchars((string) $post['created_at'], ENT_QUOTES, 'UTF-8') ?>
                ·
                <?= (int) ($post['likes_count'] ?? 0) ?> likes
            </p>
            <p><?= htmlspecialchars(strlen($post['content']) > 180 ? substr($post['content'], 0, 180) . '…' : $post['content'], ENT_QUOTES, 'UTF-8') ?></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
