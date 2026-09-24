<?php
$loggedIn = isset($_SESSION['user_id']);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if ($loggedIn): ?>
        <a href="/posts/create" class="btn btn-dark">New post</a>
    <?php endif; ?>
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
            </p>
            <p><?= htmlspecialchars(strlen($post['content']) > 180 ? substr($post['content'], 0, 180) . '…' : $post['content'], ENT_QUOTES, 'UTF-8') ?></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
