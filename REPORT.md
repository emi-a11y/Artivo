# Laboratory Activity 2: Static Laravel Interface with Bootstrap (Artivo)

Connected static prototype of the Artivo digital artwork marketplace, built from the Week 11 Figma wireframes.
No database, authentication or CRUD is used. All data comes from `app/Support/SampleData.php`.

## How to run
1. Copy the folders in this package into the Laravel project root. Only `routes/web.php` replaces an existing file (the default welcome route); everything else is new.
2. From the project root run `php artisan serve` and open http://127.0.0.1:8000.
3. If Laravel cannot find `App\Support\SampleData`, run `composer dump-autoload`.

Bootstrap 5.3.3 and the Inter font load from a CDN, so an internet connection is needed.

## Screens and routes
| Screen | URL | Route name | View |
|---|---|---|---|
| Home | `/` | `home` | `home.blade.php` |
| Browse Artworks | `/browse` | `browse` | `browse.blade.php` |
| Artwork Detail | `/artwork/{id}` | `artwork.show` | `artwork.blade.php` |
| Cart | `/cart` | `cart` | `cart.blade.php` |
| Placeholder (Artists, Sell, About, Help, Terms, Privacy) | `/page/{slug}` | `page.show` | `page.blade.php` |

The placeholder page exists only so every navigation link in the wireframes works. It has a "Back to Home" button.

## Requirement checklist
1. **Three or more screens with routes and Blade views:** four screens above, routed through `PageController`.
2. **One reusable layout:** `layouts/app.blade.php` holds the page structure. `partials/navbar`, `partials/footer`, `partials/login-modal` and `partials/artwork-card` are reused through `@include` and `@extends`.
3. **Bootstrap grid and components:** `container`, `row`/`col-*` and responsive `row-cols-*`. Components used: navbar, cards, forms, buttons, breadcrumb, pagination, dropdown, modal, collapse, alert and toast.
4. **Working navigation:** every link resolves to a route. The current page is highlighted in the navbar (`active`, `aria-current`). The logo and the "Home" breadcrumb return to the main screen.
5. **Realistic data and simulated actions:** sample artworks, artists and prices. Add, Buy Now, Checkout, Remove, Apply Filters, sort and wishlist show a toast marked "prototype". The login modal carries a "Prototype only" alert and a "Sign in (prototype)" button. A footer line states the site is a static prototype.
6. **Testing:** see below.
7. **Assets:** CSS in `public/css/artivo.css`, JS in `public/js/artivo.js`, images in `public/images/`. The brand colours are set once as Bootstrap variables at the top of the CSS file.

## Deliberate differences from the wireframes
- Realistic titles, artists and prices replace "Artwork Title / Artist Name / $12.00".
- Artwork images are yellow placeholder boxes, as in the wireframe, so no image can break. Swap them for `<img>` tags when real files exist.
- Artwork Detail: "More from this artist" is titled "More artworks you may like", because the sample data does not have five works per artist.
- Small text (prices, "Add", "View all") uses a darker orange (#C24A00) so it meets the 4.5:1 contrast ratio on white. Buttons keep the brand orange #FF6200.
- Added for the prototype requirements: a one-line "static prototype" note in the footer, the login modal, toast messages and an info alert when a search term is entered.
- On mobile the filter sidebar becomes a collapsible "Filters" button, and the navbar collapses into a menu.
- Logo: the file in `public/images/logo.svg` is a placeholder. Replace it with the real Artivo logo exported from Figma (one place: `partials/logo.blade.php`).

## Testing (desktop 1280 px and mobile 375 px)
- Every route returns 200; the controller returns 404 for unknown artwork ids and page slugs.
- No horizontal overflow on any screen at either width. One overflow on the Home hero at 375 px was found and fixed.
- Checked in a real browser: mobile navbar menu, mobile filters collapse, login modal, toast on Add, active nav state, logo returning home.
- Tested on the project's own Laravel 10 install with `php artisan serve`: `php artisan route:list` shows all routes, every URL returns 200 (404 for unknown artwork ids and page slugs), and nothing is written to `storage/logs`.
- A crawl of every link on every screen (36 URLs) found no broken links, and the browser console showed no JavaScript errors.

## Known issues
- White text on the orange buttons is about 3:1 contrast, as in the wireframe. Use dark text on orange if stricter accessibility is required.
- Search, filters, sorting and pagination are visual only; results never change.

---

# Admin side (added)

Static admin prototype built from the admin wireframe (dark sidebar, yellow top bar, white content). Same rules: no database, no login, simulated actions only.

## Admin screens and routes
| Screen | URL | Route name | View |
|---|---|---|---|
| Dashboard | `/admin` | `admin.dashboard` | `admin/dashboard.blade.php` |
| Artworks | `/admin/artworks` | `admin.artworks` | `admin/artworks.blade.php` |
| Orders (tabs: All, Pending, Paid, Refunded) | `/admin/orders` | `admin.orders` | `admin/orders.blade.php` |
| Users (tabs: All, Buyers, Artists) | `/admin/users` | `admin.users` | `admin/users.blade.php` |
| Placeholder (Categories, Genres, Reports, Settings) | `/admin/page/{slug}` | `admin.page` | `admin/page.blade.php` |

## Files added
`app/Http/Controllers/AdminController.php`, `app/Support/AdminData.php`, `resources/views/layouts/admin.blade.php`, `resources/views/admin/` (screens and partials), `public/css/admin.css`, `public/js/admin.js`. `routes/web.php` now also holds the admin routes (the storefront routes are unchanged).

## Requirement mapping
- One reusable layout: `layouts/admin.blade.php` with `sidebar`, `topbar`, `pagination`, `tabs`, `status` and `orders-table` partials.
- Bootstrap: container/grid (`row`, `col-*`), offcanvas (mobile sidebar), cards, tables (`table-responsive`), nav pills as tabs, pagination, modal (Add Artwork), form controls, toast, alert and buttons.
- Navigation: the current sidebar item is orange with `aria-current`; the logo returns to the Dashboard; "View storefront" returns to the main storefront screen.
- Simulated actions (toast marked "Prototype"): search, Edit, Delete, View, Block, and Save in the Add Artwork modal.
- Category and genre lists come from the storefront data, so both sides match.

## Differences from the admin wireframe
- Realistic names, amounts and dates replace placeholder text.
- Sales chart is a placeholder bar chart built with CSS.
- On phones the sidebar becomes an offcanvas menu opened from a hamburger button; tables scroll inside their own card.
- Logo in the sidebar uses `public/images/logo.svg` (placeholder). Replace it with the real logo, as on the storefront.

## Admin testing
All admin routes return 200 (404 for unknown slugs); no horizontal overflow at 1280 px or 375 px; mobile offcanvas, tabs, modal and toast checked in a browser; link crawl found no broken links; no JavaScript errors.
