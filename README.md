# Artivo

Artivo is a digital-artwork marketplace built with **Laravel 10**, **Bootstrap 5** and **MySQL**. Artists upload and sell digital art; buyers browse, add to cart, pay by QR code and receive a receipt. Admins manage artworks, users, orders and payments.

> Course project. Group members: **[Alon, Jeremi Joshua M.]**, **[Mendoza, Clyde Juris G.]**, **[Santos, Kurt David Andrei]**, **[Cadsawan, Dante Achilles]**
> Documentation: see the Artivo System Manual submitted in Google Classroom. Figma: **[Figma link]**

## Features

- **Storefront:** home, browse with search/filters/sorting, artwork details, wishlist, cart, checkout, My Orders, My Receipts, About/Help/Terms/Privacy pages.
- **Accounts:** register/login (Laravel Breeze), "Continue with Google" (Socialite), forgot/reset password, profile settings.
- **QR payment flow:** reference number, mobile number and receipt-image upload; admin confirms or rejects; statuses Pending / Paid / Refunded / Cancelled; unpaid orders auto-cancel after a set number of days.
- **Receipts:** every paid order gets a receipt in the buyer's account; it is also emailed when the buyer has a real email address.
- **Admin panel (`/admin`):** artworks, artists, buyers, orders, payment review, settings.
- **Artwork protection:** images are watermarked/protected until purchase.

## Requirements

PHP 8.1+, Composer, MySQL (XAMPP/Laragon/Herd), Node.js (optional, only for Vite assets), Git.

## Setup and run

```bash
git clone https://github.com/emi-a11y/Artivo.git
cd Artivo
composer install
cp .env.example .env          # Windows PowerShell: copy .env.example .env
php artisan key:generate
```

1. Start MySQL and create an empty database (for example `artivo`).
2. Open `.env` and set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for your machine.
3. Then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

4. Open http://127.0.0.1:8000 (storefront) and http://127.0.0.1:8000/admin (admin).
5. To reset the sample data: `php artisan migrate:fresh --seed`.

### Optional settings (put your own values in your local `.env`, never commit them)

- **Emailing receipts:** use `MAIL_MAILER=log` to test (emails are written to `storage/logs/laravel.log`), or set the `MAIL_*` values for Gmail SMTP with your own App Password.
- **Google login:** set `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` and `GOOGLE_REDIRECT_URI` from your own Google Cloud project.

## Sample accounts (password for all: `password`)

| Role    | Email                                                    |
| ------- | -------------------------------------------------------- |
| Admin   | admin@artivo.test                                        |
| Artists | jeremi@artivo.test, david@artivo.test, dante@artivo.test |
| Buyers  | maria@artivo.test, john@artivo.test, aira@artivo.test    |

New accounts created from "Create account" are Buyers. Only the Admin role can open `/admin`.

## Project structure (short)

- `app/Models`, `app/Http/Controllers`, `app/Http/Controllers/Admin`: logic
- `app/Mail`, `app/Notifications`, `app/Support`: receipt emails and helpers
- `resources/views`: Blade pages (storefront, admin, emails)
- `database/migrations`, `database/seeders`: tables and sample data
- `public/css`, `public/js`, `public/images`: assets

## Team contributions

| Member               | Main contributions                 |
| -------------------- | ---------------------------------- |
| [Jeremi Joshua Alon] | [main coder database, admin panel] |
| [Dante Cadsawan]     | [sub coder storefront UI]          |
| [David Santos]       | [e.g. payment flow]                |
| [Clyde Mendoza]      | [e.g. documentation, testing]      |

## Security notes

No passwords, API keys or `.env` files are stored in this repository. Sample accounts are for local testing only.

## License

Built on the Laravel framework (MIT).
