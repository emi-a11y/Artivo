<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Artivo static prototype routes (Laboratory Activity 2)
|--------------------------------------------------------------------------
| Storefront: Home, Browse, Artwork Detail, Cart (+ one generic placeholder page).
| Admin: Dashboard, Artworks, Orders, Users (+ one generic placeholder page).
| Every navigation link in the wireframes resolves to one of these routes.
*/

// ---- Storefront ----
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/browse', [PageController::class, 'browse'])->name('browse');
Route::get('/artwork/{id}', [PageController::class, 'artwork'])->whereNumber('id')->name('artwork.show');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/page/{slug}', [PageController::class, 'page'])->name('page.show');

// ---- Admin ----
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/artworks', [AdminController::class, 'artworks'])->name('artworks');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/page/{slug}', [AdminController::class, 'page'])->name('page');
});
