@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<h4 class="mb-2">Selamat Datang! 👋</h4>
<p class="mb-4">Silakan masuk ke akun Anda untuk memulai.</p>

<!-- Session Status -->
@if (session('status'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('status') }}
    </div>
@endif

<form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
    @csrf

    <!-- Email atau Username -->
    <div class="mb-3">
        <label for="login" class="form-label">Email atau Username</label>
        <input
            type="text"
            class="form-control @error('login') is-invalid @enderror"
            id="login"
            name="login"
            placeholder="Masukkan email atau username"
            value="{{ old('login') }}"
            required
            autofocus
        />
        @error('login')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
            <label class="form-label" for="password">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    <small>Lupa Password?</small>
                </a>
            @endif
        </div>
        <div class="input-group input-group-merge">
            <input
                type="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password"
                required
            />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
            <label class="form-check-label" for="remember-me"> Ingat Saya </label>
        </div>
    </div>

    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
    </div>
</form>

<p class="text-center">
    <span>Pengguna baru?</span>
    <a href="{{ route('register') }}">
        <span>Buat sebuah akun</span>
    </a>
</p>
@endsection