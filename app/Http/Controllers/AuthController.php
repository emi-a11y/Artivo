<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** The login form is a modal, so /login (used by the auth middleware) just opens it. */
    public function showLogin()
    {
        return redirect()->route('home')->with('openLogin', true);
    }

    public function login(Request $request)
    {
        $credentials = $request->validateWithBag('login', [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials + ['status' => 'Active'], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Wrong email or password, or the account is blocked.'], 'login')
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('status', 'Welcome back, ' . Auth::user()->name . '.');
        }

        return back()->with('status', 'Welcome back, ' . Auth::user()->name . '.');
    }

    public function register(Request $request)
    {
        $data = $request->validateWithBag('register', [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'Buyer',
            'status' => 'Active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return back()->with('status', 'Account created. Welcome to Artivo, ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }
}
