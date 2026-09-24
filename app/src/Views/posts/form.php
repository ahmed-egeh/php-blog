<?php
declare(strict_types=1);

use App\ViewModels\PostFormPage;

/** @var PostFormPage $page */
$post = $page->post;
$titleValue = $post?->title->value ?? '';
$contentValue = $post?->content->value ?? '';
$selectedCategoryId = $post?->categoryId->value ?? 0;
?>
<section style="max-width: 720px;">
    <h1 class="mb-4"><?= htmlspecialchars($page->heading, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($page->categories->isEmpty()): ?>
        <p class="text-muted">No categories in the database yet. Seed categories first.</p>
    <?php else: ?>
        <form action="<?= htmlspecialchars($page->action, ENT_QUOTES, 'UTF-8') ?>" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($titleValue, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Choose…</option>
                    <?php foreach ($page->categories as $category): ?>
                        <option
                            value="<?= $category->id->value ?>"
                            <?= $selectedCategoryId === $category->id->value ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($category->name->value, ENT_QUOTES, 'UTF-8') ?>
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
                ><?= htmlspecialchars($contentValue, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="btn btn-dark">Save</button>
            <a href="/posts" class="btn btn-link">Cancel</a>
        </form>
    <?php endif; ?>
</section>
