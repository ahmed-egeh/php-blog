<?php
declare(strict_types=1);

use App\ViewModels\ResetPasswordPage;

/** @var ResetPasswordPage $page */
?>
<form id="resetPasswordForm" action="/reset-password" method="POST">
  <h1 class="h4 mb-3">Reset password</h1>
  <input type="hidden" name="token" value="<?= htmlspecialchars($page->token, ENT_QUOTES, 'UTF-8') ?>">

  <div class="form-group">
    <label for="password">New password</label>
    <input
      type="password"
      class="form-control"
      id="password"
      name="password"
      required
    >
  </div>

  <div class="form-group">
    <label for="passwordConfirmation">Confirm new password</label>
    <input
      type="password"
      class="form-control"
      id="passwordConfirmation"
      name="password_confirmation"
      required
    >
  </div>

  <button type="submit" class="btn btn-primary mt-2">Reset password</button>

  <div class="mt-3">
    <a href="/login">Back to login</a>
  </div>
</form>

<script>
const form = document.getElementById('resetPasswordForm');

form.addEventListener('submit', async (event) => {
  event.preventDefault();

  const formData = new FormData(form);
  const password = formData.get('password');
  const passwordConfirmation = formData.get('password_confirmation');

  if (password !== passwordConfirmation) {
    alert('Passwords do not match.');
    return;
  }

  try {
    const { ok, message } = await submitApiForm('/user/reset-password', formData);

    alert(message);
    if (ok) {
      window.location.href = '/login';
    }
  } catch ({ name, message }) {
    console.error({ name, message });
    alert(message);
  }
});
</script>
