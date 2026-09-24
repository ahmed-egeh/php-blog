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
        <?php $showLikes = false; include __DIR__ . '/_card.php'; ?>
    <?php endforeach; ?>
<?php endif; ?>
