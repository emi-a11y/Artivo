# Artivo: Laravel + Bootstrap prototype with a working database

Storefront and admin side built from the Figma wireframes, now backed by a database (Eloquent models, migrations and a small seeder).

## How to run
1. Copy the folders in this package into the Laravel project root (`C:\laravel`). `routes/web.php` and `app/Models/User.php` replace existing files; everything else is new.
2. Start MySQL (XAMPP or Laragon) and make sure the database named in `.env` (`DB_DATABASE`) exists.
3. In the project root run:
   - `php artisan migrate --seed` (creates the tables and a few starting records)
   - `php artisan serve`
4. Open http://127.0.0.1:8000 (storefront) or http://127.0.0.1:8000/admin (admin).
5. To start over with clean sample data: `php artisan migrate:fresh --seed`.

The logo is read from `public/images/artivologo.png` (one place: `resources/views/partials/logo.blade.php`). Bootstrap 5.3.3 and the Inter font load from a CDN, so an internet connection is needed.

## Sample accounts (all use the password `password`)
| Role | Email |
|---|---|
| Admin | admin@artivo.test |
| Artists | mika@artivo.test, hana@artivo.test, dev@artivo.test |
| Buyers | maria@artivo.test, john@artivo.test, aira@artivo.test |

New accounts can be created from the storefront ("Log in" then "Create account"); they are Buyers. Only the Admin role can open `/admin`.

## Database
| Table | Holds |
|---|---|
| `users` | name, email, hashed password, role (Admin/Artist/Buyer), status (Active/Blocked) |
| `categories`, `genres` | the filter lists, shared by the storefront and the admin forms |
| `artworks` | title, artist, category, genre, price, file info, description, rating, status (Published/Pending/Draft) |
| `orders`, `order_items` | one order per checkout; items keep the title and price paid, even if the artwork is deleted later |
| `wishlists` | artworks a user saved |

Starting data: 7 accounts, 4 categories, 4 genres, 8 artworks and 4 orders. The shopping cart lives in the session.

## Storefront screens
| Screen | URL | Database use |
|---|---|---|
| Home | `/` | categories, top-rated and newest published artworks, artists |
| Browse | `/browse` | working search, category/genre/price filters, sorting, pagination |
| Artwork Detail | `/artwork/{id}` | artwork, license choice, Add to Cart, Buy Now, wishlist, related artworks |
| Cart | `/cart` | cart items, remove, checkout (creates an order; login required; payment is simulated) |
| Placeholder pages | `/page/{slug}` | none (Artists, Sell, About, Help, Terms, Privacy) |

## Admin screens (Admin role only)
| Screen | URL | What works |
|---|---|---|
| Dashboard | `/admin` | totals, six-month sales chart, top-selling artworks and recent orders, all from the database |
| Artworks | `/admin/artworks` | search, category/genre filters, add, edit, delete (modal form with validation) |
| Categories / Genres | `/admin/categories`, `/admin/genres` | add and delete (a list used by artworks cannot be deleted) |
| Orders | `/admin/orders` | status tabs, change an order's status |
| Users | `/admin/users` | role tabs, block and unblock |
| Reports / Settings | `/admin/page/{slug}` | placeholder pages so every sidebar link works |

## Requirement checklist
1. Routes, controllers and Blade views for every screen above (`PageController`, `CartController`, `AuthController`, `WishlistController`, `AdminController`, `Admin\ArtworkController`, `Admin\TaxonomyController`).
2. Reusable layouts: `layouts/app.blade.php` (storefront) and `layouts/admin.blade.php` (admin) with shared partials for navbar, footer, login modal, artwork card, pagination, sidebar and top bar.
3. Bootstrap grid and components: container, row/col, navbar, offcanvas (mobile admin menu), cards, tables, tabs and pills, pagination, modal, dropdown, forms, alerts and toasts.
4. Navigation: the current page is highlighted (`active`, `aria-current`); the logo and breadcrumbs return to the main screen; "View storefront" returns from the admin.
5. Assets: CSS in `public/css/artivo.css` and `public/css/admin.css`, JS in `public/js/`, images in `public/images/`.

## Differences from the wireframes
- Realistic names, prices and dates replace placeholder text.
- Artwork images are yellow placeholder boxes, as in the wireframe. Image upload is not part of this version.
- Admin top bar uses the brand orange (#FF6200) with dark text, and the logo sits directly on the dark sidebar.
- Small text (prices, links) uses a darker orange (#C24A00) for readable contrast; buttons keep #FF6200.
- On phones the storefront filters collapse into a button and the admin sidebar becomes an offcanvas menu; wide tables scroll inside their own card.
- Added for the database version: login/register modal, wishlist, license choice, flash messages (toasts), Categories and Genres pages in the admin.

## Testing (desktop 1280 px and phone 375 px)
Run on the project's own Laravel 10 install with a fresh `migrate:fresh --seed`; 58 automated browser checks passed:
- Guest cart, register, add/remove cart items, wishlist, checkout creating an order, blocked access to `/admin` for buyers, wrong-password message, blocked users unable to log in.
- Search, category filter and pagination on Browse.
- Admin: add, edit (modal pre-filled), validation error reopening the modal, delete, draft artworks hidden from the storefront, order status change and tabs, user block, category add and delete rules.
- No horizontal overflow, broken images or JavaScript errors on any storefront or admin screen at either width.
- Not tested here: a live MySQL server was not available in the test environment, so the checks ran on SQLite with the same migrations and seeder. The schema uses only standard Laravel column types.

## Known limitations
- Payment is simulated; no real payment, email or file download is implemented.
- Orders can be refunded by changing the status, but no money moves.
- White text on the orange buttons is about 3:1 contrast, as in the wireframe.
