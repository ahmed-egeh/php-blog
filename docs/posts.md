# Posts

Posts are the **articles** on Space Blog. Anyone can read published ones. Only the author can edit or delete their own.

A new post is published right away (`active = 1`). Titles must be unique per author. The public list shows **5 posts per page**.

## Routes

| Method | Path | Who | What happens |
| --- | --- | --- | --- |
| `GET` | `/posts` | Anyone | Published posts, 5 per page (`?page=` for later pages) |
| `GET` | `/posts/{id}` | Anyone | One post (unpublished posts only for the owner) |
| `GET` | `/posts/mine` | Logged in | Your posts, including unpublished |
| `GET` | `/posts/create` | Logged in | New post form |
| `POST` | `/posts` | Logged in | Save a new post |
| `GET` | `/posts/{id}/edit` | Logged in | Edit form (owner only) |
| `POST` | `/posts/{id}` | Logged in | Update a post (owner only) |
| `POST` | `/posts/{id}/delete` | Logged in | Soft-delete a post (owner only) |

Guests who open a “must be logged in” URL are sent to `/login`.

If you try to edit someone else’s post, you are sent back to `/posts` with a flash message.

A missing or unpublished post (when you are not the owner) responds with **404** and the text `Post not found.`

## Writing a post

The form asks for title (max 100 characters), category, and body. Categories come from the `categories` table (seeded in `db_seeds/categories.sql`).

There is no likes or comments feature.

## Images on cards

If a post has no image path in the database, the page shows a placeholder illustration based on the post id.
