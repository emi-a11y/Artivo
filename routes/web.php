<?php

use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ArtworkController as AdminArtworkController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Artivo routes (database-backed school project)
|--------------------------------------------------------------------------
*/

// ---- Storefront ----
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/browse', [PageController::class, 'browse'])->name('browse');
Route::get('/artwork/{id}', [PageController::class, 'artwork'])->whereNumber('id')->name('artwork.show');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/page/{slug}', [PageController::class, 'page'])->name('page.show');

Route::post('/cart/add/{id}', [CartController::class, 'add'])->whereNumber('id')->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->whereNumber('id')->name('cart.remove');
Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::post('/wishlist/{id}', [WishlistController::class, 'toggle'])->whereNumber('id')->name('wishlist.toggle');
Route::get('/wishlist', [WishlistController::class, 'index'])->middleware('auth')->name('wishlist');
// ---- Accounts ----
// Login, register and logout come from Laravel Breeze's controllers (routes/auth.php).
   require __DIR__ . '/auth.php';
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
 // Breeze's dashboard route: signed-out visitors are sent to the login page.
   Route::get('/dashboard', function () {
       return redirect(auth()->user()->isAdmin() ? route('admin.dashboard') : route('home'));
   })->middleware('auth')->name('dashboard');
// ---- Paying for an order with a QR code ----
    Route::middleware('auth')->group(function () {
        Route::get('/orders/{order}/pay', [PaymentController::class, 'show'])->whereNumber('order')->name('orders.pay');
        Route::post('/orders/{order}/pay', [PaymentController::class, 'submit'])->whereNumber('order')->name('orders.pay.submit');
        Route::post('/orders/{order}/cancel', [PaymentController::class, 'cancel'])->whereNumber('order')->name('orders.cancel');
        Route::get('/orders/{order}/receipt', [PaymentController::class, 'receipt'])->whereNumber('order')->name('orders.receipt');
    });
Route::get('/orders', [OrderController::class, 'index'])
    ->middleware('auth')
    ->name('orders');

// ---- Admin (Admin role only) ----
Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/artworks', [AdminArtworkController::class, 'index'])->name('artworks');
    Route::post('/artworks', [AdminArtworkController::class, 'store'])->name('artworks.store');
    Route::put('/artworks/{artwork}', [AdminArtworkController::class, 'update'])->whereNumber('artwork')->name('artworks.update');
    Route::delete('/artworks/{artwork}', [AdminArtworkController::class, 'destroy'])->whereNumber('artwork')->name('artworks.destroy');

    Route::get('/categories', [TaxonomyController::class, 'index'])->defaults('type', 'categories')->name('categories');
    Route::post('/categories', [TaxonomyController::class, 'store'])->defaults('type', 'categories')->name('categories.store');
    Route::delete('/categories/{id}', [TaxonomyController::class, 'destroy'])->defaults('type', 'categories')->whereNumber('id')->name('categories.destroy');
    Route::get('/genres', [TaxonomyController::class, 'index'])->defaults('type', 'genres')->name('genres');
    Route::post('/genres', [TaxonomyController::class, 'store'])->defaults('type', 'genres')->name('genres.store');
    Route::delete('/genres/{id}', [TaxonomyController::class, 'destroy'])->defaults('type', 'genres')->whereNumber('id')->name('genres.destroy');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->whereNumber('order')->name('orders.update');

    Route::get('/payments', [PaymentReviewController::class, 'index'])->name('payments');
    Route::post('/payments/{order}/confirm', [PaymentReviewController::class, 'confirm'])->whereNumber('order')->name('payments.confirm');
    Route::post('/payments/{order}/reject', [PaymentReviewController::class, 'reject'])->whereNumber('order')->name('payments.reject');

    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('methods');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('methods.store');
    Route::get('/payment-methods/{method}/edit', [PaymentMethodController::class, 'edit'])->whereNumber('method')->name('methods.edit');
    Route::put('/payment-methods/{method}', [PaymentMethodController::class, 'update'])->whereNumber('method')->name('methods.update');
    Route::delete('/payment-methods/{method}', [PaymentMethodController::class, 'destroy'])->whereNumber('method')->name('methods.destroy');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/status', [AdminController::class, 'toggleUser'])->whereNumber('user')->name('users.status');

    Route::get('/page/{slug}', [AdminController::class, 'page'])->name('page');
});
