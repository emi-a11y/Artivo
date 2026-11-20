<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

/** Forgot password: email a reset link, then choose a new password. */
class PasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'Enter the email of your account.',
            'email.email' => 'Enter a valid email address.',
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            Log::error('Password reset mail failed: ' . $e->getMessage());

            return back()->withInput()->withErrors(['email' => 'The email could not be sent right now. Please try again later.']);
        }

        // Same answer whether or not the email exists, so nobody can probe which emails have accounts.
        return back()->with('status', 'If that email has an account, a reset link is on its way. It expires in 60 minutes.');
    }

    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'Use at least 6 characters.',
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
        }

        return redirect()->route('login')->with('status', 'Password changed. You can log in now.');
    }
}
