<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Admin\ArtistPhotoController;
use App\Http\Controllers\DownloadController;
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

// Artists (Storefront)
Route::get('/artists', [ArtistController::class, 'index'])->name('artists');

Route::get('/artwork/{id}', [PageController::class, 'artwork'])->whereNumber('id')->name('artwork.show');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/page/{slug}', [PageController::class, 'page'])->name('page.show');


Route::post('/cart/add/{id}', [CartController::class, 'add'])->whereNumber('id')->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->whereNumber('id')->name('cart.remove');
Route::post('/cart/remove-selected', [CartController::class, 'removeSelected'])->name('cart.removeSelected');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
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
    Route::post('/orders/{order}/receipt/email', [\App\Http\Controllers\ReceiptEmailController::class, 'send'])->middleware('throttle:3,1')->whereNumber('order')->name('orders.receipt.email');
    Route::post('/email/verify-address', [\App\Http\Controllers\EmailVerificationController::class, 'send'])->middleware('throttle:3,1')->name('email.verify.send');
    Route::get('/orders/{order}/items/{item}/download', [DownloadController::class, 'show'])->whereNumber(['order', 'item'])->name('orders.download');
    Route::get('/receipts', [\App\Http\Controllers\ReceiptInboxController::class, 'index'])->name('receipts');
});

Route::get('/email/verify-address/{id}/{hash}', [\App\Http\Controllers\EmailVerificationController::class, 'confirm'])->middleware('signed')->whereNumber('id')->name('email.verify.confirm');

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
    Route::post('/categories/{id}/image', [TaxonomyController::class, 'image'])->whereNumber('id')->name('categories.image');
    Route::delete('/categories/{id}/image', [TaxonomyController::class, 'removeImage'])->whereNumber('id')->name('categories.image.remove');

    Route::get('/genres', [TaxonomyController::class, 'index'])->defaults('type', 'genres')->name('genres');
    Route::post('/genres', [TaxonomyController::class, 'store'])->defaults('type', 'genres')->name('genres.store');
    Route::delete('/genres/{id}', [TaxonomyController::class, 'destroy'])->defaults('type', 'genres')->whereNumber('id')->name('genres.destroy');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->whereNumber('order')->name('orders.update');

    Route::get('/payments', [PaymentReviewController::class, 'index'])->name('payments');
    Route::post('/payments/{order}/confirm', [PaymentReviewController::class, 'confirm'])->name('payments.confirm');
    Route::post('/payments/{order}/reject', [PaymentReviewController::class, 'reject'])->name('payments.reject');

    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('methods');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('methods.store');
    Route::get('/payment-methods/{method}/edit', [PaymentMethodController::class, 'edit'])->whereNumber('method')->name('methods.edit');
    Route::put('/payment-methods/{method}', [PaymentMethodController::class, 'update'])->name('methods.update');
    Route::delete('/payment-methods/{method}', [PaymentMethodController::class, 'destroy'])->name('methods.destroy');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/status', [AdminController::class, 'toggleUser'])->whereNumber('user')->name('users.status');

    // Artists (Admin)
    Route::get('/artists', [ArtistPhotoController::class, 'index'])->name('artists');
    Route::post('/artists/{user}/photo', [ArtistPhotoController::class, 'store'])->whereNumber('user')->name('artists.photo');
    Route::delete('/artists/{user}/photo', [ArtistPhotoController::class, 'destroy'])->whereNumber('user')->name('artists.photo.remove');

    Route::patch('/artists/{user}/name', [ArtistPhotoController::class, 'rename'])->whereNumber('user')->name('artists.rename');
    Route::post('/artists', [ArtistPhotoController::class, 'add'])->name('artists.add');
    Route::delete('/artists/{user}', [ArtistPhotoController::class, 'delete'])->whereNumber('user')->name('artists.delete');

    Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
    Route::patch('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::patch('/settings/store', [SettingsController::class, 'store'])->name('settings.store');

    Route::get('/page/{slug}', [AdminController::class, 'page'])->name('page');
});
