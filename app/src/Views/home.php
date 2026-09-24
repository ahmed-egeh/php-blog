<div class="text-end mb-4">
    <a href="/posts" class="all-posts-link">All posts</a>
</div>

<?php if ($posts === []): ?>
    <p class="text-muted">No posts yet.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <?php include __DIR__ . '/posts/_card.php'; ?>
    <?php endforeach; ?>
<?php endif; ?>
