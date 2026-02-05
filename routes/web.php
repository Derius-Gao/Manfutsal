<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccessController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Guest routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Dashboard
Route::middleware(['auth', 'permission:dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Bookings - dengan validasi permission
Route::middleware(['auth', 'permission:bookings'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::put('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::put('/bookings/{id}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::put('/bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{id}/upload-payment', [PaymentController::class, 'uploadProof'])->name('bookings.uploadPayment');
});

// Users - dengan validasi permission
Route::middleware(['auth', 'permission:users'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    // Route create harus sebelum route dengan parameter {id}
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::post('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
});

// Lapangans - dengan validasi permission
Route::middleware(['auth', 'permission:lapangans'])->group(function () {
    Route::get('/lapangans', [LapanganController::class, 'index'])->name('lapangans.index');
    // Route create harus sebelum route dengan parameter {id}
    Route::middleware(['role:admin|superadmin'])->group(function () {
        Route::get('/lapangans/create', [LapanganController::class, 'create'])->name('lapangans.create');
        Route::post('/lapangans', [LapanganController::class, 'store'])->name('lapangans.store');
    });
    Route::get('/lapangans/{id}', [LapanganController::class, 'show'])->name('lapangans.show');
    // CRUD routes untuk admin/superadmin
    Route::middleware(['role:admin|superadmin'])->group(function () {
        Route::get('/lapangans/{id}/edit', [LapanganController::class, 'edit'])->name('lapangans.edit');
        Route::put('/lapangans/{id}', [LapanganController::class, 'update'])->name('lapangans.update');
        Route::delete('/lapangans/{id}', [LapanganController::class, 'destroy'])->name('lapangans.destroy');
        Route::post('/lapangans/{id}/toggle-status', [LapanganController::class, 'toggleStatus'])->name('lapangans.toggleStatus');
    });
});

// Activities - dengan validasi permission
Route::middleware(['auth', 'permission:activities'])->group(function () {
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activities.show');
});

// Settings - dengan validasi permission
Route::middleware(['auth', 'permission:settings'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// Hak Akses - dengan validasi permission
Route::middleware(['auth', 'permission:access'])->group(function () {
    Route::get('/access', [AccessController::class, 'index'])->name('access.index');
    Route::get('/access/permissions/{userId}', [AccessController::class, 'getUserPermissions'])->name('access.permissions');
    Route::post('/access/permissions/update', [AccessController::class, 'updatePermissions'])->name('access.permissions.update');
});

// Keuangan - dengan validasi permission
Route::middleware(['auth', 'permission:keuangan'])->group(function () {
    Route::get('/keuangan', [PaymentController::class, 'index'])->name('keuangan.index');
    Route::post('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{id}/reject', [PaymentController::class, 'rejectPayment'])->name('payments.reject');
    Route::match(['get', 'post'], '/keuangan/export', [ExportController::class, 'exportKeuangan'])->name('keuangan.export');
});

// Redirect root
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');
