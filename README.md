# LGU-Link

LGU Norzagaray citizen & admin portal, built with PHP + MySQL on XAMPP.

## Project structure

```
LGU-Link/
├── index.php              # Public homepage (guests) or redirect to dashboard (logged in)
├── auth/                  # login.php, register.php, logout.php
├── admin/                 # Admin-only pages (dashboard, news, appointments, departments, document-request, reports, citizens-charter)
├── user/                  # Citizen-only pages (dashboard, citizens-charter browse/search, chatbot-api)
├── includes/              # Shared PHP partials (auth.php, headers, modals, charter-helpers.php, charter-admin.php, news-helpers.php, news-admin.php)
├── config/                # database.php (PDO connection)
├── assets/
│   ├── css/                # style.css (shared) + one file per page
│   ├── js/                 # main.js (shared) + one file per page
│   ├── img/                 # logo
│   └── uploads/news/        # admin-uploaded news photos (gitignored, auto-created)
└── sql/schema.sql          # Database schema + seed accounts
```

## Setup (XAMPP)

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Import the schema: open phpMyAdmin, go to the **Import** tab, and upload `sql/schema.sql`
   (or run `"C:\xampp\mysql\bin\mysql.exe" -u root < sql/schema.sql` from this folder).
3. Seed the Citizen's Charter table: `php sql/migrate-citizens-charter-data.php`
   (one-time; after this, admins manage the data via `admin/citizens-charter.php`, not the file).
4. Visit **http://localhost/LGU-Link/**.

## Default accounts

| Role  | Email                        | Password    |
|-------|-------------------------------|-------------|
| Admin | admin@norzagaray.gov.ph       | Admin@123   |
| User  | juan.delacruz@example.com     | User@123    |

Citizens can also self-register at `/auth/register.php`. Admin accounts are seeded directly in the database and are not self-service.

## Notes

- `config/database.php` assumes the default XAMPP MySQL credentials (`root`, no password). Update it if yours differ.
- Passwords are hashed with PHP's `password_hash()` (bcrypt).
- Document requests, appointments, and department data on the admin pages are still static demo data — wiring them to real tables is the next step.
- The Citizen's Charter (`citizens_charter_services` table) is real and DB-backed: admins manage it at `admin/citizens-charter.php`; citizens browse/search it under `user/citizens-charter*.php`; the chatbot (`user/chatbot-api.php`) reads the same table for both its rule-based fallback and its Claude system prompt. `includes/citizens-charter-data.php` is no longer read by the app — it only exists as the original seed source for `sql/migrate-citizens-charter-data.php`.
- The public homepage (`index.php`, shown to anyone not logged in) is real and DB-backed too: admins manage posts at `admin/news.php` (or the "Post News" button available on every admin page), including a photo upload, a category, and a "show in homepage carousel" toggle. Posts marked featured appear in the top fade carousel; every post appears in the "Latest News" grid below it (carousel falls back to the newest posts if none are marked featured, so it's never empty once at least one post exists). Uploaded photos are validated server-side (real image check, 5MB cap, JPG/PNG/GIF/WebP only) and stored in `assets/uploads/news/`, which the app creates automatically.
