async function submitApiForm(url, formData) {
  const response = await fetch(url, {
    method: 'POST',
    body: formData,
    credentials: 'same-origin',
  });

  let payload = {
    success: false,
    message: 'Something went wrong.',
  };

  try {
    payload = await response.json();
  } catch {
    payload.message = 'Invalid server response.';
  }

  const ok = response.ok && payload.success === true;
  const message = payload.message || (ok ? 'Done.' : 'Request failed.');

  return { ok, message, payload };
}
