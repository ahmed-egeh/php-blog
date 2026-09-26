<?php
declare(strict_types=1);

use App\Domain\User\User;
use App\ViewModels\PostsIndexPage;

/** @var ?User $currentUser */
/** @var PostsIndexPage $page */
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

<?php
$pagination = $page->pagination;
if ($pagination !== null && $pagination->shouldShow()):
?>
<div class="mt-4" aria-label="Posts pages">
    <ul class="pagination justify-content-center">
        <li class="page-item<?= $pagination->hasPrevious() ? '' : ' disabled' ?>">
            <?php if ($pagination->hasPrevious()): ?>
                <a class="page-link" href="<?= htmlspecialchars($pagination->url($pagination->page - 1), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
            <?php else: ?>
                <span class="page-link">Previous</span>
            <?php endif; ?>
        </li>
        <?php for ($n = 1; $n <= $pagination->lastPage; $n++): ?>
            <li class="page-item<?= $pagination->isCurrent($n) ? ' active' : '' ?>">
                <?php if ($pagination->isCurrent($n)): ?>
                    <span class="page-link" aria-current="page"><?= $n ?></span>
                <?php else: ?>
                    <a class="page-link" href="<?= htmlspecialchars($pagination->url($n), ENT_QUOTES, 'UTF-8') ?>"><?= $n ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>
        <li class="page-item<?= $pagination->hasNext() ? '' : ' disabled' ?>">
            <?php if ($pagination->hasNext()): ?>
                <a class="page-link" href="<?= htmlspecialchars($pagination->url($pagination->page + 1), ENT_QUOTES, 'UTF-8') ?>">Next</a>
            <?php else: ?>
                <span class="page-link">Next</span>
            <?php endif; ?>
        </li>
    </ul>
</div>
<?php endif; ?>
