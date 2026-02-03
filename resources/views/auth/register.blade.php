@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="text-center mb-4">
    <h3><i class="fas fa-user-plus me-2"></i>Register</h3>
    <p class="text-muted">Buat akun baru</p>
</div>

<form method="POST" action="{{ route('register.store') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ old('name') }}" required autofocus>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email') }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirmation" 
                       name="password_confirmation" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">No. HP (Opsional)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="{{ old('phone') }}">
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="address" class="form-label">Alamat (Opsional)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <input type="text" class="form-control" id="address" name="address" 
                       value="{{ old('address') }}">
            </div>
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="terms" required>
        <label class="form-check-label" for="terms">
            Saya setuju dengan <a href="#" class="text-decoration-none">syarat dan ketentuan</a>
        </label>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Register
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Sudah punya akun? 
        <a href="{{ route('login') }}" class="text-decoration-none">
            Login di sini
        </a>
    </p>
</div>
@endsection
