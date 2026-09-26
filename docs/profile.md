# Profile

The profile page is for **your own account**, after you are logged in.

You can change first name, last name, and photo. **Username and email stay as they are.** You can also change your password or hide the account.

## Routes

All of these require a logged-in user. Guests are sent to `/login`.

| Method | Path | What happens |
| --- | --- | --- |
| `GET` | `/profile` | Settings form |
| `POST` | `/profile` | Save name and optional photo |
| `POST` | `/profile/password` | Change password (current + new + confirm) |
| `POST` | `/profile/delete` | Soft-delete the account (needs current password) |

## Photo

The photo field accepts JPG, PNG, or WebP, up to **2 MB**. Files are stored under `app/public/uploads/avatars/`. If you skip the file input, the existing photo is kept.

## Delete account

The account is marked deleted, not wiped from the database. Posts you already published stay on the site. You are logged out afterwards.
