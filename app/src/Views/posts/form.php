<section style="max-width: 720px;">
    <h1 class="mb-4"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($categories === []): ?>
        <p class="text-muted">No categories in the database yet. Seed categories first.</p>
    <?php else: ?>
        <form action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Choose…</option>
                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= (int) $post['category_id'] === (int) $category['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea
                    class="form-control"
                    id="content"
                    name="content"
                    rows="10"
                    required
                ><?= htmlspecialchars((string) $post['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="btn btn-dark">Save</button>
            <a href="/posts" class="btn btn-link">Cancel</a>
        </form>
    <?php endif; ?>
</section>
