@extends('auth.layout')
@section('title', 'Choose a new password')

@section('card')
    <h2 class="auth-title">Choose a new password</h2>
    <p class="auth-sub">Pick a password you have not used before. At least 6 characters.</p>

    <form action="{{ route('password.store') }}" method="post" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label for="rpEmail" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="rpEmail" name="email"
                value="{{ old('email', $email) }}" autocomplete="email" required>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rpPassword" class="form-label">New password</label>
            <div class="auth-pass">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="rpPassword"
                    name="password" minlength="6" autocomplete="new-password" required autofocus>
                <button type="button" class="auth-toggle" data-toggle-pass="rpPassword" aria-label="Show password">Show</button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rpPassword2" class="form-label">Confirm new password</label>
            <input type="password" class="form-control" id="rpPassword2" name="password_confirmation" minlength="6"
                autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Save new password</button>
    </form>
    <p class="auth-switch"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
