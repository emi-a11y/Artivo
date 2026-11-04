<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** Separate login / register page. ?tab=register opens the sign-up form. */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect(Auth::user()->isAdmin() ? route('admin.dashboard') : route('home'));
        }

        // Remember where to send the visitor after logging in:
        // 1) ?back=<url> from the navbar link, 2) a page the auth middleware already saved, 3) the previous page.
        $back = (string) $request->query('back');
        $previous = url()->previous();
        if ($back !== '' && Str::startsWith($back, url('/'))) {
            $request->session()->put('url.intended', $back);
        } elseif (! $request->session()->has('url.intended') && Str::startsWith($previous, url('/'))) {
            $request->session()->put('url.intended', $previous);
        }

        return view('auth.login', [
            'tab' => $request->query('tab') === 'register' ? 'register' : 'login',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Please enter your email.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        if (! Auth::attempt($credentials + ['status' => 'Active'], $request->boolean('remember'))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Wrong email or password, or the account is blocked.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        return redirect($this->destination($request, $user))
            ->with('status', 'Welcome back, ' . $user->name . '.');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'That email is already registered. Try logging in instead.',
            'password.required' => 'Please choose a password.',
            'password.min' => 'Your password must be at least 6 characters.',
            'password.confirmed' => 'The passwords do not match.',
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

        return redirect($this->destination($request, $user))
            ->with('status', 'Account created. Welcome to Artivo, ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }

    /** Admins go to the dashboard; everyone else goes back where they were. */
    private function destination(Request $request, User $user): string
    {
        if ($user->isAdmin()) {
            $request->session()->forget('url.intended');

            return route('admin.dashboard');
        }

        $to = $request->session()->pull('url.intended');

        return ($to && ! Str::contains($to, ['/admin', '/login', '/register'])) ? $to : route('home');
    }
}
