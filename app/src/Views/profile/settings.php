<section style="max-width: 640px;">
    <h1 class="mb-4">Profile settings</h1>

    <form action="/profile" method="POST" enctype="multipart/form-data" class="mb-5">
        <div class="mb-3">
            <?php if (!empty($user['user_image'])): ?>
                <img
                    src="/<?= htmlspecialchars((string) $user['user_image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="Profile photo"
                    width="96"
                    height="96"
                    class="rounded-circle mb-2"
                    style="object-fit: cover;"
                >
            <?php endif; ?>
            <label for="user_image" class="form-label">Photo</label>
            <input type="file" class="form-control" id="user_image" name="user_image" accept="image/jpeg,image/png,image/webp">
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required
                   value="<?= htmlspecialchars((string) $user['first_name'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required
                   value="<?= htmlspecialchars((string) $user['last_name'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars((string) $user['username'], ENT_QUOTES, 'UTF-8') ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
        </div>

        <button type="submit" class="btn btn-dark">Save profile</button>
    </form>

    <h2 class="h4">Change password</h2>
    <form action="/profile/password" method="POST" class="mb-5">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-outline-dark">Update password</button>
    </form>

    <h2 class="h4 text-danger">Delete account</h2>
    <p class="text-muted">This hides your account. Posts you wrote stay on the site.</p>
    <form action="/profile/delete" method="POST" onsubmit="return confirm('Delete your account?');">
        <div class="mb-3">
            <label for="delete_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="delete_password" name="password" required>
        </div>
        <button type="submit" class="btn btn-outline-danger">Delete account</button>
    </form>
</section>
