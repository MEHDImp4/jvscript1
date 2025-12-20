# Profile Database (WA4E Assignment)

Small PHP CRUD app for the WA4E Profile Database assignment. Uses PDO, session-based flash messages, and POST-Redirect-GET everywhere.

## Setup
1. Create a MySQL schema (defaults to `misc`). Update credentials in `pdo.php` if needed.
2. Load the schema: `mysql -u root -p misc < schema.sql`.
3. Place these files on a PHP-capable server (Apache + PHP recommended).
4. Browse to `login.php` and use the seeded account `umsi@umich.edu / php123`.

## Pages
- `index.php` — public list of profiles; shows add/edit/delete when logged in.
- `login.php` / `logout.php` — authentication with salted MD5 hash (salt `XyZzy12*_`).
- `add.php` — create profile (requires login).
- `edit.php` — update profile (requires login + ownership check).
- `delete.php` — confirmation screen then delete via POST (requires login + ownership).
- `view.php` — public read-only view.

## Validation
- Login page: JavaScript alert if email/password missing or email lacks `@`.
- Server-side: all profile fields required; email must contain `@`.

## Notes
- Titles include "Mehdi" as required by the assignment spec.
- Flash messages are stored in the session (`util.php`).
- Ownership is enforced on edit/delete using `user_id`.
