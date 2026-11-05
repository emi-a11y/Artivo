<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** Wrong attempts allowed before the temporary lock. */
    private const MAX_ATTEMPTS = 5;

    /** How long (seconds) the login stays locked after too many wrong attempts. */
    private const LOCK_SECONDS = 60;

    /** How long (seconds) wrong attempts are remembered. */
    private const ATTEMPT_WINDOW = 900;

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

        // A lock survives a page refresh: look it up again from the saved key.
        $lockout = 0;
        if ($key = $request->session()->get('login_lock_key')) {
            $lockout = $this->lockSecondsLeft($key);
            if ($lockout === 0) {
                $request->session()->forget('login_lock_key');
            }
        }

        return view('auth.login', [
            'tab' => $request->query('tab') === 'register' ? 'register' : 'login',
            'lockout' => $lockout,
            'lockSeconds' => self::LOCK_SECONDS,
            'maxAttempts' => self::MAX_ATTEMPTS,
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

        $key = Str::lower($credentials['email']) . '|' . $request->ip();

        // 1) Locked? Refuse before even looking at the password.
        if ($this->lockSecondsLeft($key) > 0) {
            return $this->lockedResponse($request, $key);
        }

        $user = User::where('email', $credentials['email'])->first();

        // 2) Tell the visitor exactly what was wrong.
        if (! $user) {
            return $this->failed($request, $key, 'email', 'No account was found with that email.');
        }
        if ($user->status === 'Blocked') {
            return redirect()->route('login')
                ->withErrors(['email' => 'This account has been blocked. Please contact the admin.'])
                ->onlyInput('email');
        }
        if (! Hash::check($credentials['password'], $user->password)) {
            return $this->failed($request, $key, 'password', 'Incorrect password.');
        }

        // 3) Success.
        RateLimiter::clear($key);
        Cache::forget($this->lockCacheKey($key));
        $request->session()->forget('login_lock_key');

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

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

    /** Count a wrong attempt; lock the login when the limit is reached. */
    private function failed(Request $request, string $key, string $field, string $message)
    {
        RateLimiter::hit($key, self::ATTEMPT_WINDOW);

        if (RateLimiter::attempts($key) >= self::MAX_ATTEMPTS) {
            RateLimiter::clear($key);
            Cache::put($this->lockCacheKey($key), now()->addSeconds(self::LOCK_SECONDS)->timestamp, self::LOCK_SECONDS);

            return $this->lockedResponse($request, $key);
        }

        $left = self::MAX_ATTEMPTS - RateLimiter::attempts($key);

        return redirect()->route('login')
            ->withErrors([$field => $message])
            ->with('attempts_left', $left)
            ->onlyInput('email');
    }

    private function lockedResponse(Request $request, string $key)
    {
        $request->session()->put('login_lock_key', $key);

        return redirect()->route('login')->onlyInput('email');
    }

    private function lockCacheKey(string $key): string
    {
        return 'login-lock:' . sha1($key);
    }

    private function lockSecondsLeft(string $key): int
    {
        $until = Cache::get($this->lockCacheKey($key));

        return $until ? max(0, $until - now()->timestamp) : 0;
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
