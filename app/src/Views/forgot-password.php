<?php declare(strict_types=1); ?>
<form id="forgotPasswordForm" action="/forgot-password" method="POST">
  <h1 class="h4 mb-3">Forgot password</h1>
  <p class="text-muted">Enter your email and we will send a reset link if an account exists.</p>

  <div class="form-group">
    <label for="forgotEmail">Email address</label>
    <input name="email" type="email" class="form-control" id="forgotEmail" required>
  </div>

  <button type="submit" class="btn btn-primary mt-2">Send reset link</button>

  <div class="mt-3">
    <a href="/login">Back to login</a>
  </div>
</form>

<script>
const form = document.getElementById('forgotPasswordForm');

form.addEventListener('submit', async (event) => {
  event.preventDefault();

  const formData = new FormData(form);
  const email = formData.get('email');

  if (!email) {
    alert('Please fill out the form.');
    return;
  }

  try {
    const { ok, message } = await submitApiForm('/user/forgot-password', formData);

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
