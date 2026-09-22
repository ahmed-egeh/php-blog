<form id="loginForm" action="/login" method="POST">
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input name="password" type="password" class="form-control" id="exampleInputPassword1" required>
  </div>
  <div class="form-group form-check">
    <input name="rememberMe" type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Remember me</label>
  </div>
  <div>
    <a href="/signup">Sign up</a>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script>
const form = document.getElementById('loginForm');

form.addEventListener('submit', async (event) => {
  event.preventDefault();

  const formData = new FormData(form);

  const email = formData.get('email');
  const password = formData.get('password');
  const rememberMe = formData.get('rememberMe');

 if (!email || !password) {
    alert('Please fill out the form.');
    return;
  }

  try {
    const { ok, message } = await submitApiForm('/user/login', formData);

    if (!ok) {
      alert(message);
      return;
    }

    alert(message);
    window.location.href = '/';
  } catch ({ name, message }) {
    console.error({ name, message });
    alert(message);
  }
});
</script>