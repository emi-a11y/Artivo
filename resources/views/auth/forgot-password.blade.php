@extends('auth.layout')
@section('title', 'Forgot password')

@section('card')
    <h2 class="auth-title">Forgot your password?</h2>
    <p class="auth-sub">Enter the email of your account and we will send you a link to choose a new one.</p>

    @if (session('status'))
        <div class="alert alert-success py-2 small" role="status">{{ session('status') }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="post" novalidate>
        @csrf
        <div class="mb-3">
            <label for="fpEmail" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="fpEmail" name="email"
                value="{{ old('email') }}" placeholder="name@example.com" autocomplete="email" required autofocus>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Send reset link</button>
    </form>
    <p class="auth-switch">Remembered it? <a href="{{ route('login') }}">Back to log in</a></p>
@endsection
