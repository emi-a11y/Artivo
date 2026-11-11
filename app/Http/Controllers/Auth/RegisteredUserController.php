<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class RegisteredUserController extends Controller
{
    /** The register form is the "Register" tab of the shared login page. */
    public function create(): RedirectResponse
    {
        return redirect()->route('login', ['tab' => 'register']);
    }

    /** Validate the form, save the new account in the users table, then log it in. */
    public function store(Request $request): RedirectResponse
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

        event(new Registered($user));
        $this->sendWelcome($user);

        Auth::login($user);
        $request->session()->regenerate();

        $to = $request->session()->pull('url.intended');
        $to = ($to && ! Str::contains($to, ['/admin', '/login', '/register'])) ? $to : route('home');

        return redirect($to)->with('status', 'Account created. Welcome to Artivo, ' . $user->name . '.');
    }

    /** A mail problem must never stop someone from signing up, so errors are only logged. */
    private function sendWelcome(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
