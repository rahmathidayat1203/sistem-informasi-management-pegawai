@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<h4 class="mb-2">Mulai Perjalanan Anda 🚀</h4>
<p class="mb-4">Buat akun baru untuk masuk ke sistem.</p>

<form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
    @csrf

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            id="name"
            name="name"
            placeholder="Masukkan nama lengkap Anda"
            value="{{ old('name') }}"
            required
            autofocus
        />
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            placeholder="Masukkan email Anda"
            value="{{ old('email') }}"
            required
        />
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Username -->
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input
            type="text"
            class="form-control @error('username') is-invalid @enderror"
            id="username"
            name="username"
            placeholder="Masukkan username unik"
            value="{{ old('username') }}"
            required
        />
        @error('username')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3 form-password-toggle">
        <label class="form-label" for="password">Password</label>
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

    <!-- Confirm Password -->
    <div class="mb-3 form-password-toggle">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <div class="input-group input-group-merge">
            <input
                type="password"
                id="password_confirmation"
                class="form-control"
                name="password_confirmation"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password"
                required
            />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
    </div>

    <button class="btn btn-primary d-grid w-100">Sign up</button>
</form>

<p class="text-center">
    <span>Sudah punya akun?</span>
    <a href="{{ route('login') }}">
        <span>Login di sini</span>
    </a>
</p>
@endsection