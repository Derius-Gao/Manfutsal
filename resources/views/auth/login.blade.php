@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="text-center mb-4">
    <h3><i class="fas fa-sign-in-alt me-2"></i>Login</h3>
    <p class="text-muted">Masuk ke akun Anda</p>
</div>

<form method="POST" action="{{ route('login.store') }}">
    @csrf
    
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" 
                   value="{{ old('email') }}" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            Ingat saya
        </label>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Belum punya akun? 
        <a href="{{ route('register') }}" class="text-decoration-none">
            Daftar di sini
        </a>
    </p>
</div>

<div class="text-center mt-4">
    <small class="text-muted">
        <strong>Demo Accounts:</strong><br>
        Customer: customer@demo.com / password<br>
        Manager: manager@manfutsal.com / password<br>
        Admin: admin@manfutsal.com / password<br>
        Super Admin: superadmin@manfutsal.com / password
    </small>
</div>
@endsection
