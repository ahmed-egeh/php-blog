# Space Blog

This project is a **re-visit of vanilla PHP**.

It is a small space-themed blog built with **PHP 8.4**, Apache, MariaDB, and Docker. There is **no Laravel**, no Composer app framework, and no Blade. Routing, views, sessions, mail, and the database layer are written by hand so the moving parts stay visible.

## What you can do here

Guests can read posts and create an account. After they confirm their email, they can log in, edit a profile photo and name, write posts, and reset a forgotten password.

Sign-up, login, and password reset talk to JSON endpoints from the browser. Everything else is a normal HTML page.

## How to run it

1. Copy `.env.example` to `.env` and fill in the database passwords. Keep `APP_URL=http://localhost:8080` unless you change the port.
2. Start Docker:

   ```bash
   docker compose up -d
   ```

3. Load the SQL files in `db_seeds/` into MariaDB (host port **4306**, database from `.env`). A typical order is `users.sql`, `remember_token.sql`, `categories.sql`, `posts.sql`, `posts_articles.sql`.
4. Open [http://localhost:8080](http://localhost:8080). Activation and reset emails show up in [Mailpit](http://localhost:8025).

The Apache document root is `app/public/`. PHP classes under `App\` load from `app/src/` through `app/autoload.php`.

## Features and routes

| Feature | What it is | Details |
| --- | --- | --- |
| [Home and about](docs/home.md) | Front page and a short “about” page | `/`, `/about` |
| [Accounts](docs/accounts.md) | Sign up, activate, log in, remember me, log out, forgot / reset password | `/login`, `/signup`, `/forgot-password`, `/reset-password`, `/user/*` |
| [Profile](docs/profile.md) | Name, photo, password, delete account | `/profile` |
| [Posts](docs/posts.md) | Read, write, edit, and delete articles | `/posts` |

`/playground` exists only as an empty stub. It is not a product feature.

## How the code is arranged

In plain words:

- **Domain** — rules and names (user, post, password, email).
- **Application** — “do this job” services (register, publish a post).
- **Infrastructure** — PDO, SMTP, cookies, files, `.env`.
- **HTTP** — router, controllers, views.

Controllers do not create those services with `new`. A small container in `app/src/Infrastructure/Container.php` wires them together.

## Config you actually need

| Variable | Used for |
| --- | --- |
| `APP_URL` | Absolute links in emails (activate, reset password) |
| `DB_HOST`, `DB_PORT`, `MARIADB_*` | Database |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_FROM` | Outgoing mail (Mailpit in Docker) |
