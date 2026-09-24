<article style="max-width: 720px;">
    <p class="text-muted small mb-2">
        <?= htmlspecialchars($post['category_name'], ENT_QUOTES, 'UTF-8') ?>
        ·
        <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name'], ENT_QUOTES, 'UTF-8') ?>
        ·
        <?= htmlspecialchars((string) $post['created_at'], ENT_QUOTES, 'UTF-8') ?>
    </p>
    <h1 class="mb-4"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="mb-4" style="white-space: pre-wrap;"><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></div>

    <?php if ($isOwner): ?>
        <div class="d-flex gap-2">
            <a href="/posts/<?= (int) $post['id'] ?>/edit" class="btn btn-outline-dark">Edit</a>
            <form action="/posts/<?= (int) $post['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this post?');">
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    <?php endif; ?>

    <p class="mt-4"><a href="/posts">Back to posts</a></p>
</article>
