<?php

use App\Http\Controllers\Admin\ArtworkController as AdminArtworkController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

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

// ---- Accounts ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/status', [AdminController::class, 'toggleUser'])->whereNumber('user')->name('users.status');

    Route::get('/page/{slug}', [AdminController::class, 'page'])->name('page');
});
