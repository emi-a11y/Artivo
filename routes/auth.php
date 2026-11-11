<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication routes (Laravel Breeze controllers, Artivo design)
|--------------------------------------------------------------------------
| Login and Register share one page (resources/views/auth/login.blade.php).
*/

// Registration: the form is the "Register" tab of the login page.
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// Login (with the 5-attempt lockout inside the controller).
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.attempt');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
