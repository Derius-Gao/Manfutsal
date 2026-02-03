@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-users me-2"></i>Users</h5>
                                <h3>0</h3>
                                <small>Total Users</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-map me-2"></i>Lapangan</h5>
                                <h3>0</h3>
                                <small>Total Lapangan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar me-2"></i>Bookings</h5>
                                <h3>0</h3>
                                <small>Total Booking</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-money-bill-wave me-2"></i>Revenue</h5>
                                <h3>{{ formatRupiah(0) }}</h3>
                                <small>Total Pendapatan</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Management Actions</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.user.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.lapangan.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-map me-2"></i>Kelola Lapangan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-calendar me-2"></i>Semua Booking
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.keuangan.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-money-bill-wave me-2"></i>Keuangan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
