<form id="registerForm" action="/signup" method="POST">

  <div class="form-group">
    <label for="firstName">First name</label>
    <input
      type="text"
      class="form-control"
      id="firstName"
      name="first_name"
      required
    >
  </div>

  <div class="form-group">
    <label for="lastName">Last name</label>
    <input
      type="text"
      class="form-control"
      id="lastName"
      name="last_name"
      required
    >
  </div>

  <div class="form-group">
    <label for="email">Email address</label>
    <input
      type="email"
      class="form-control"
      id="email"
      name="email"
      aria-describedby="emailHelp"
      required
    >
    <small id="emailHelp" class="form-text text-muted">
      We'll never share your email with anyone else.
    </small>
  </div>

  <div class="form-group">
    <label for="password">Password</label>
    <input
      type="password"
      class="form-control"
      id="password"
      name="password"
      required
    >
  </div>

  <div class="form-group">
    <label for="passwordConfirmation">Confirm password</label>
    <input
      type="password"
      class="form-control"
      id="passwordConfirmation"
      name="password_confirmation"
      required
    >
  </div>

  <button type="submit" class="btn btn-primary">
    Sign up
  </button>

  <div class="mt-3">
    <span>Already have an account?</span>
    <a href="/login">Log in</a>
  </div>

</form>

<script>
const form = document.getElementById('registerForm');

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
    const { ok, message } = await submitApiForm('/user/signup', formData);

    if (!ok) {
      alert(message);
      return;
    }

    alert(message);
    window.location.href = '/login';
  } catch ({ name, message }) {
    console.error({ name, message });
    alert(message);
  }
});
</script>