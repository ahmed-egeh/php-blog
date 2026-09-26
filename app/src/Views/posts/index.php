<?php
declare(strict_types=1);

use App\Domain\User\User;

/** @var ?User $currentUser */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <?php if ($currentUser !== null): ?>
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
