# Accounts

This is everything about **who you are on the site**: creating an account, confirming email, logging in, staying logged in, and resetting a forgotten password.

Pages are HTML. The forms post to JSON endpoints (`success` + `message`) the same way login and sign-up already work.

## Pages

| Method | Path | Who | What happens |
| --- | --- | --- | --- |
| `GET` | `/signup` | Guests | Sign-up form |
| `GET` | `/login` | Guests | Login form (remember-me checkbox, link to forgot password) |
| `GET` | `/forgot-password` | Guests | Ask for a reset email |
| `GET` | `/reset-password` | Guests | Set a new password (needs `?token=` from the email) |
| `GET` | `/user/logout` | Anyone | End the session and forget the remember-me cookie |

If you are already logged in, the guest pages above send you home instead.

A reset link with a missing token sends you back to `/forgot-password`.

## JSON / email actions

| Method | Path | Who | What happens |
| --- | --- | --- | --- |
| `POST` | `/user/signup` | Guests | Create the user, email an activation link |
| `GET` | `/user/activate` | Guests | Confirm the account (`?token=` from the email) |
| `POST` | `/user/login` | Guests | Check email + password, start a session |
| `POST` | `/user/forgot-password` | Anyone | If the email belongs to an **activated** account, send a reset link |
| `POST` | `/user/reset-password` | Guests | Set a new password from the token |

## How it works, in short

1. **Sign up** needs first name, last name, email, and a password (at least 8 characters, with a letter and a number). Username is generated for you. You cannot pick it.
2. An activation email is sent. The link uses `APP_URL` from `.env` and is valid for **24 hours**. Until you click it, login is blocked.
3. **Login** can tick **Remember me** (a cookie for 30 days, stored as a hash in the database).
4. **Forgot password** always answers with the same success text, so it does not reveal whether an email is registered. The reset link is valid for **one hour** and can be used only once.
5. After a successful reset, remember-me tokens for that user are cleared.

Open [Mailpit](http://localhost:8025) to read activation and reset mail while Docker is running.
