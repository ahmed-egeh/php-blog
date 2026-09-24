<?php
declare(strict_types=1);

use App\Services\AuthService;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0"><?= htmlspecialchars($page->heading, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if (AuthService::loggedInUserId() !== null): ?>
        <a href="/posts/create" class="btn btn-dark">New post</a>
    <?php endif; ?>
</div>

<?php if ($page->posts->isEmpty()): ?>
    <p class="text-muted">No posts yet.</p>
<?php else: ?>
    <?php foreach ($page->posts as $post): ?>
        <?php include __DIR__ . '/_card.php'; ?>
    <?php endforeach; ?>
<?php endif; ?>
